<?php
/*
 *
 * Copyright (c) 2012, Martin Vondra
 * All Rights Reserved.
 * DESCRIPTION
 */

namespace Leolos;


/**
 * Dispatcher
 * @author Martin Vondra <martin.vondra@email.cz>
 * Custom main request handler.
 *
 */
class Dispatcher {
	protected $dispatchTable;
	protected $check;
    protected $init;
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
     * setApplicationConfigObject(mixed $config)
     * hold config object/array
     * @param mixed $config
     */
    public function setApplicationConfigObject($config) {
        if ($config) {
            $this->request->setApplicationConfigObject($config);
        }
    }

    /**
     * setApplicationInitObject(object $init)
     * hold init object - main share init function could be placed there
     * main called method is 'init()'
     * @param object $init
     */
    public function setApplicationInitObject($init) {
        if (is_object($init)) {
            $this->init = $init;
        }
    }

    /**
     * setApplicationCheckObject(object $check)
     * hold check object- object whit public method check() which is usefull
     * for some rutines as autorization, rights checks etc.
     * @param object $check
     */
    public function setApplicationCheckObject($check) {
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
		Importer::import($this->handler->modules);

		try {
			$startTime = microtime();
            $this->customInit();
			$this->checkRequest();
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
        //print_r($this->request);die();
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

    private function customInit() {
        if ($this->init) {
            $this->init->init($this->request);
        }
    }
}
