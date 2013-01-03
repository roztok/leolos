<?php
/*
 * 
 * Copyright (c) 2012, Martin Vondra
 * All Rights Reserved.
 * DESCRIPTION
 */

namespace Leolos\Publisher;


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
    *
    *
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
* Dispatcher
* @author Martin Vondra <martin.vondra@email.cz>
* Custom main request handler. 
* 
*/
class Dispatcher {
	private $METHOD;
	private $PATH;
	public $form;
	protected $dispatchTable;
	public $config;
	public $check;
	private $handler;

	
	/**
	* public _constructor()
	* 
	*/
	public function __construct() {
        $this->dispatchTable = array();
        # create new formfield instance to parse URL fields
        $this->form = new Fieldstorage\FormField($this->METHOD);

        /* getting a method type - GET or POST, URI path */
        $this->METHOD = $_SERVER["REQUEST_METHOD"];
        if (array_key_exists("PATH_INFO", $_SERVER)) {
            $this->PATH = $_SERVER["PATH_INFO"];
        }
    }

    public function setAplicationConfigObject($config) {
        if ($config) {
            $this->config = $config;
        }
    }
    
    public function setAplicationCheckObject($check) {
        if (is_object($check)) {
            $this->check = $check;
        }
    }

    public function addHandler($handler) {
        $this->dispatchTable[$handler->uri] = $handler;
    }

    private function findHandler() {
        #looking for handler
        $this->handler = Null;
        if (array_key_exists($this->PATH, $this->dispatchTable)) {
            $this->handler = & $this->dispatchTable[$this->PATH];
            return True;
        } else {
            # using reqular expresion ?
            foreach ($this->dispatchTable AS $key => $value) {
                //error_log($key);
                if (@preg_match("/^$key$/", $this->PATH)) {
                    $handler = & $this->dispatchTable[$key];
                    return True;
                }
            }
            # use default handler, if exist
            if ($this->handler === Null && array_key_exists(PP_DEFAULT_PATH, $this->dispatchTable)) {
                $this->PATH = PP_DEFAULT_PATH;
                $this->handler = & $this->dispatchTable[$this->PATH];
                return True;
            }
        }
        return False;
    }

    private function checkHTTPMethodAllowed() {
        # checking HTTP method
        if ($this->METHOD != $this->handler->allowedMethod and $this->handler->allowedMethod != PP_ALLOW_GET_POST) {
            Status\Status::METHOD_NOT_ALLOWED();
            return False;
        } else {
            return True;
        }
    }

    private function checkRequest() {
        # check request
        if($this->handler->checkFlag and $check) {
            $this->check = $check;
            $this->check->check($this);
        }
    }
    
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
			call_user_func($this->handler->methodName, $this);
			$endtime = microtime();
			error_log('leolos::Publisher:callTime::'.($endtime-$startTime).'s');
			return True;
		} catch (Exception $e) {
			/* known exception from dispatchTable */
			/*if(isset($path[4]) && array_key_exists(get_class($e), $path[4])) {
				/* handle exception myself by handler 
				return call_user_func($path[4][get_class($e)], $this);
			} else */
			if(method_exists($e, "handleRequest")) {
				error_log('leolos::Publisher:exceptionHandler::'.$e);
// 				echo '<!--'; echo $e->getTraceAsString(); echo '-->';
				$e->handleRequest($this);
				return True;
			}
			error_log('leolos::Publisher:unexpected exception:'.$e);
// 			echo "<!-- ".$e." -->";
// 			echo '<!--'; echo $e->getTraceAsString(); echo '-->';
			throw $e;
		}
	}
}
