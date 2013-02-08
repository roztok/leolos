<?php
/*
*
* Copyright (c) 2012, Martin Vondra.
* All Rights Reserved.
*
* DESCRIPTION
* Database comunication module
* Create and hold a db connection
* Manage transactions
* Execute queries
*
* @author Martin Vondra <martin.vondra@email.cz>
*/

namespace Leolos\MysqlDb;

/**
 * MysqlError
 * @author Martin Vondra <martin.vondra@email.cz>
 */
class MysqlError extends \ErrorException {

    /**
        * public __construct($message, $code)
        * Create new exception
        *
        * @param string $message
        * @param int $code
        */
    public function __construct($message, $code) {
        parent::__construct($message, $code);
        $this->traceString = parent::__toString();
    }

    /**
        * Text description of exception
        */
    public function __toString() {
        return "<".get_class($this).": [".$this->code."]:".$this->message.".>\n";
    }
}

/**
 * DuplicateEntryError
 * indicates duplicate entry error - unique index fault
 * @author Martin Vondra <martin.vondra@email.cz>
 */
class DuplicateEntryError extends MysqlError {
}


/**
 *
 */
interface MysqlDbLogger {

    public function info($msg);
    public function debug($msg);
    public function warning($msg);
    public function error($msg);
}


/**
 * standard dummy system logger
 */
class stdLogger implements MysqlDbLogger {
    public function info($msg) {
        syslog(\LOG_INFO, $msg);
    }
    public function error($msg) {
        syslog(\LOG_ERR, $msg);
    }
    public function warning($msg) {
        syslog(\LOG_WARNING, $msg);
    }
    public function debug($msg) {
        syslog(\LOG_DEBUG, $msg);
    }
}


/**
 * MysqlDBConfig
 * Configuration for MysqlDb object
 * @author Martin Vondra <martin.vondra@email.cz>
 *
 */
class MysqlDbConfig {
    private $m_hostname;
    private $m_port;
    private $m_user;
    private $m_password;
    private $m_databaseName;
    private $m_encoding;
    private $m_socket;
    private $m_connectionTimeOut;
    private $m_autocommit;
    private $m_logger;

    /**
        * constructor
        * set default properties
        */
    public function __construct(& $parser=Null) {
        $this->setHostname("localhost");
        $this->setPort(3306);
        $this->setEncoding("utf8");
        $this->setSocket("/var/run/mysqld/mysqld.sock");
        $this->setConnectionTimeOut(2);
        $this->setAutocommit(False);
        $this->m_logger = new stdLogger();

        if ($parser) {
            $this->setHostname($parser->get("mysql", "Host"));
            $this->setPort($parser->getInt("mysql", "Port", 3306));
            $this->setEncoding($parser->get("mysql", "Encoding", "utf8"));
            $this->setSocket($parser->get("mysql", "Socket", "/var/run/mysqld/mysqld.sock"));
            $this->setUser($parser->get("mysql", "User"));
            $this->setPassword($parser->get("mysql", "Password"));
            $this->setDatabaseName($parser->get("mysql", "Database"));
        }
    }

    public function getLogger() {
        return $this->m_logger;
    }
    public function setLogger($logger) {
        $this->m_logger = $logger;
    }

    public function setHostname($host) {
        $this->m_hostname = $host;
    }
    public function getHostname() {
        return $this->m_hostname;
    }

    public function setPort($port) {
        $this->m_port = $port;
    }
    public function getPort() {
        return $this->m_port;
    }

    public function setUser($user) {
        $this->m_user = $user;
    }
    public function getUser() {
        return $this->m_user;
    }

    public function setPassword($passwd) {
        $this->m_password = $passwd;
    }
    public function getPassword() {
        return $this->m_password;
    }

    public function setDatabaseName($dbname) {
        $this->m_databaseName = $dbname;
    }
    public function getDatabaseName() {
        return $this->m_databaseName;
    }

    public function setEncoding($encoding) {
        $this->m_encoding = $encoding;
    }
    public function getEncoding() {
        return $this->m_encoding;
    }

    public function setSocket($socket) {
        $this->m_socket = $socket;
    }
    public function getSocket() {
        return $this->m_socket;
    }

    public function setConnectionTimeOut($timeout) {
        $this->m_connectionTimeOut = $timeout;
    }
    public function getConnectionTimeOut() {
        return $this->m_connectionTimeOut;
    }

    public function setAutocommit($flag) {
        $this->m_autocommit = $flag;
    }
    public function getAutocommit() {
        return $this->m_autocommit;
    }
}


/**
 * MysqlDb
 * Modul for comunication with database MySQL
 *
 * @author Martin Vondra <martin.vondra@email.cz>
 *
 */
class MysqlDb {

    private $host;
    private $port;
    private $user;
    private $passwd;
    private $dbname;
    private $encoding;
    private $socket;
    private $connectionTimeOut;
    public $connection;
    private $inTransaction;
    private $explainMode;
    private $query;
    private $logger;

    /**
        * Constructor
        * @param MysqlDbConfig $config
        */
    public function __construct(MysqlDbConfig $config) {
        $this->host = $config->getHostname();
        $this->port = $config->getPort();
        $this->user = $config->getuser();
        $this->passwd = $config->getPassword();
        $this->dbname = $config->getDatabaseName();
        $this->encoding = $config->getEncoding();
        $this->socket = $config->getSocket();
        $this->connectionTimeOut = $config->getConnectionTimeOut();
        $this->autocommit = $config->getAutocommit();
        $this->logger = $config->getLogger();
        //$this->connect();
        $this->inTransaction = False;
        $this->explainMode = False;
    }

    /**
        * callQuery(string $query)
        * Make real query to database
        * @param string $query sql query
        */
    private function callQuery($query = Null) {
        if ($query) {
            $this->logger->info($query);
            $this->connection->real_query($query);
        } else {
            $this->logger->info($this->query);
            $this->connection->real_query($this->query);
        }
        return $this->connection->store_result();
    }

    /**
        * renderQuery(string $args)
        * Render and escape sql query
        * @param string $args arguments to rendering
        * @return string
        */
    private function renderQuery($args) {
        /*all given arguments as array*/
        $converted_args = array();

        /*first argument is sql query, others are params for rendering*/
        $query_part = $args[0];
        for ($i=1;$i<count($args);$i++) {
            if (is_string($args[$i])) {
                /*escape dangerous chars*/
                $args[$i] = "'".$this->connection->escape_string($args[$i])."'";
            } elseif ($args[$i] === Null) {
                /*translate Null type to db NULL*/
                $args[$i] = "NULL";
            } elseif ($args[$i] === False) {
                $args[$i] = 0;
            }

            array_push($converted_args, $args[$i]);
        }
        return vsprintf($query_part, $converted_args);
    }

    /**
        * explainQuery()
        * Explain a query in explain mode
        * Try to find some performance defects
        */
    protected function explainQuery() {
        $res = $this->callQuery("EXPLAIN ".$this->query);
        while ($row = $res->fetch_object()) {
            if (stristr($row->Extra, "temporary") ||
                    stristr($row->Extra, "filesort") ||
                    stristr($row->Extra, "buffer")) {
                # log Extra property of explain as Warning
                $this->logger->warning("Explain found: ".$row->Extra);
            }
        }
    }

    /**
        * enableExplainMode()
        * Set explain mode to enable
        * All queries will be explained, log warning when find some troubles
        */
    public function enableExplainMode() {
        $this->explainMode = True;
    }

    /**
        * disableExplainMode()
        * Set explain mode to disable
        */
    public function disableExplainMode() {
        $this->explainMode = False;
    }

    /**
        * connect()
        * Connect to server and hold a connection
        * Method set connection timeout and encoding type
        * @throws MysqlError
        */
    public function connect() {
        /* init mysqli object */
        $this->connection = mysqli_init();

        /* connection timeout */
        $this->connection->options(MYSQLI_OPT_CONNECT_TIMEOUT,
                $this->connectionTimeOut);

        /* connecting to server */
        if ($this->host == "localhost") {
            $this->connection->real_connect("localhost", $this->user,
                    $this->passwd, $this->dbname, $this->port, $this->socket);
        } else {
            $this->connection->real_connect($this->host, $this->user,
                    $this->passwd, $this->dbname, $this->port, $this->socket);
        }
        /* check connection */
        if (mysqli_connect_errno()) {
            throw new MysqlError(mysqli_connect_error(),
                    mysqli_connect_errno());
        }

        /* setting charset */
        $this->connection->set_charset($this->encoding);

        /* disable autocommit */
        $this->connection->autocommit($this->autocommit);
    }

    /**
        * begin()
        * Start a new transaction, if not in a transaction.
        * For lost connections, try 3 times to reconnect. When reconnect faild,
        * raise an exception.
        * @throws MysqlError
        */
    public function begin() {
        $i = 0;
        while(1) {
            try {
                if(!$this->connection->query("START TRANSACTION")) {

                    throw new MysqlError($this->connection->error,
                            $this->connection->errno);
                }
            } catch (Exception $e) {
                /* reconnect */
                $this->connect();
                if(++$i < 3) continue;
                throw $e;
            }
            break;
        }
        $this->inTransaction = True;
    }

    /**
        * commit()
        * Commit current transaction
        */
    public function commit() {
        if(!$this->inTransaction)
            throw new MysqlError("You must start transaction at first",500);

        $this->connection->query("COMMIT");
        $this->inTransaction = False;
    }

    /**
        * rollback()
        * Cancel current transaction
        */
    public function rollback() {
        $this->connection->query("ROLLBACK");
        $this->inTransaction = False;
    }

    /**
        * execute()
        * Rendering a query, escape given params to be save, and execute the query
        * @param string $query sql query with sprintf syntax
        * @param mixed $paramX parametr for sprintf conversion
        * @link http://cz1.php.net/manual/en/function.sprintf.php
        * @return mysqli_result
        * @throws MysqlError
        */
    public function execute() {

        //echo $query_part;echo "\n<br>";
        //TODO: explain mode - explane the query and check optimalisations
        $args = func_get_args();
        $this->query = $this->renderQuery($args);
        //$this->logger->info($this->query);
        $res = $this->callQuery();
        if ($this->explainMode) {
            $this->explainQuery();
        }
        //echo $this->query;echo "<br>";

        if($this->connection->error) {
            if ($this->connection->errno == 1062) {
                /* unique index fault */
                throw new DuplicateEntryError($this->connection->error,
                        $this->connection->errno);

            } else {
                throw new MysqlError($this->connection->error,
                        $this->connection->errno);
            }
        }
        return $res;
    }

    /**
        * lastInsertId()
        * Getting the last inseret id
        * @return int $insert_id
        */
    public function lastInsertId() {
        return $this->connection->insert_id;
    }

    /**
        * private __destruct()
        * Rollback any uncommited transactions and free the connection
        *
        */
    public function __destruct() {
        /* rollback all uncomitted transactions */
        $this->rollback();
        /* leave connection */
        $this->connection->close();
    }

    public function __wakeup() {
        $this->connect();
    }
}
