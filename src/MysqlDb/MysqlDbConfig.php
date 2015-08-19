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
		$this->m_logger = new StdLogger();

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
