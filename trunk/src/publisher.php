<?php
/*
 *
 * Copyright (c) 2012, Martin Vondra
 * All Rights Reserved.
 * DESCRIPTION
 */

namespace Leolos;


/* load modules */

//get include path from enviroment
$includePath = getenv('LeolosIncludePath');
if(!$includePath) {
    $includePath = "";
}

include $includePath."status.php";
include $includePath."fieldstorage.php";
include $includePath."importer.php";


/**
* FunctionHandler
* @author Martin Vondra <martin.vondra@email.cz>
* reguest handler by php function
*/
class FunctionHandler {

    public $uri;
    public $methodName;
    public $allowedMethod;
    public $includes;
    public $checkFlag;


    /**
    * Constructor
    * @param string $uri URL path to handle exp. from "localhost/testPage" is "/testPage"
    * @param string $methodName aplication function name to call
    * @param string $allowedMethod which HTTP method is allowed: GET, POST, GET|POST
    * @param array $includes list of aplication includes - include safety
    * @param boolean $checkFlag indicate if we need to call check object by publisher
    */
    public function __construct($uri, $methodName, $allowedMethod, $includes, $checkFlag) {
        $this->uri = $uri;
        $this->methodName = $methodName;
        $this->allowedMethod = $allowedMethod;
        $this->includes = $includes;
        $this->checkFlag = $checkFlag;
    }
}


/**
* Request
* @author Martin Vondra <martin.vondra@email.cz>
*
*/
class Request {
    private $METHOD;
    private $PATH;
    public $form;
    public $config;

    /**
    * Constructor
    *
    */
    public function __construct() {
         /* getting a method type - GET or POST, URI path */
        $this->METHOD = $_SERVER["REQUEST_METHOD"];
        if (array_key_exists("PATH_INFO", $_SERVER)) {
            $this->PATH = $_SERVER["PATH_INFO"];
        }
        # create new formfield instance to parse URL fields
        $this->form = new Fieldstorage\FormField($this->METHOD);
    }

    /**
    * setAplicationConfigObject(mixed $config)
    * hold config object/array
    * @param mixed $config
    */
    public function setAplicationConfigObject($config) {
        $this->config = $config;
    }

    /**
    * getPath()
    * @return string
    */
    public function getPath() { return $this->PATH; }

    /**
    * getMethod()
    * @return string
    */
    public function getMethod() { return $this->METHOD; }
}


/**
* Dispatcher
* @author Martin Vondra <martin.vondra@email.cz>
* Custom main request handler.
*
*/
class Dispatcher {
	protected $dispatchTable;
	protected $check;
	private $handler;
	private $request;


	/**
	* Constructor
	*
	*/
	public function __construct() {
        $this->dispatchTable = array();
        $this->request = new Request();
    }

    /**
    * setAplicationConfigObject(mixed $config)
    * hold config object/array
    * @param mixed $config
    */
    public function setAplicationConfigObject($config) {
        if ($config) {
            $this->request->setAplicationConfigObject($config);
        }
    }

    /**
    * setAplicationCheckObject(object $check)
    * hold check object- object whit public method check() which is usefull
    * for some rutines as autorization, rights checks etc.
    * @param object $check
    */
    public function setAplicationCheckObject($check) {
        if (is_object($check)) {
            $this->check = $check;
        }
    }

    /**
    * addHandler(object $handler)
    * Register a handler to dispatchTable
    * @param obejct $handler
    */
    public function addHandler($handler) {
        $this->dispatchTable[$handler->uri] = $handler;
    }

    /**
    * handleRequest()
    *
    *
    */
    public function handleRequest() {

		if (!$this->findHandler()) {
            Status\Status::NOT_FOUND();
            return False;
        }

        if(!$this->checkHTTPMethodAllowed()) {
            return False;
        }

        # import all modules
		Importer::import($this->handler->includes);

		$this->checkRequest();

		try {
			$startTime = microtime();
			call_user_func($this->handler->methodName, $this->request);
			$endtime = microtime();
			error_log('leolos::Publisher:callTime::'.($endtime-$startTime).'s');
		} catch (\Exception $e) {
			/* known exception from dispatchTable */
			/*if(isset($path[4]) && array_key_exists(get_class($e), $path[4])) {
				/* handle exception myself by handler
				return call_user_func($path[4][get_class($e)], $this);
			} else */
			if(method_exists($e, "handleRequest")) {
				error_log('leolos::Publisher:exceptionHandler::'.$e);
// 				echo '<!--'; echo $e->getTraceAsString(); echo '-->';
				$e->handleRequest($this->request);
				return True;
			}
			error_log('leolos::Publisher:unexpected exception:'.$e);
// 			echo "<!-- ".$e." -->";
// 			echo '<!--'; echo $e->getTraceAsString(); echo '-->';
			throw $e;
		}
		return True;
	}

    private function findHandler() {
        #looking for handler
        $this->handler = Null;
        if (array_key_exists($this->request->getPath(), $this->dispatchTable)) {
            $this->handler = & $this->dispatchTable[$this->request->getPath()];
            return True;
        } else {
            # using reqular expresion ?
            foreach ($this->dispatchTable AS $key => $value) {
                //error_log($key);
                if (@preg_match("/^$key$/", $this->request->getPath())) {
                    $handler = & $this->dispatchTable[$key];
                    return True;
                }
            }
            # use default handler, if exist
            if ($this->handler === Null && array_key_exists("*", $this->dispatchTable)) {
                $this->handler = & $this->dispatchTable["*"];
                return True;
            }
        }
        return False;
    }

    private function checkHTTPMethodAllowed() {
        # checking HTTP method
        if ($this->request->getMethod() != $this->handler->allowedMethod and
            $this->handler->allowedMethod != "GET|POST") {
            Status\Status::METHOD_NOT_ALLOWED();
            return False;
        } else {
            return True;
        }
    }

    private function checkRequest() {
        # check request
        if($this->handler->checkFlag and $this->check) {
            $this->check->checkRequest($this->request);
        }
    }
}
