<?php
/*
 *
* Copyright (c) 2012, Martin Vondra
* All Rights Reserved.
* DESCRIPTION
*/

namespace Leolos;

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
    public $modules;


	/**
	 * Constructor
	 * @param string $uri URL path to handle exp. from "localhost/testPage" is "/testPage"
	 * @param string $methodName aplication function name to call
	 * @param string $allowedMethod which HTTP method is allowed: GET, POST, GET|POST
	 * @param boolean $checkFlag indicate if we need to call check object by publisher
	 */
	public function __construct($uri, $methodName, $allowedMethod, $modules, $checkFlag) {
		$this->uri = $uri;
		$this->methodName = $methodName;
		$this->allowedMethod = $allowedMethod;
		$this->checkFlag = $checkFlag;
        $this->modules = $modules;
	}
}
