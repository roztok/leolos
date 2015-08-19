<?php
/*
 *
 * Copyright (c) 2012, Martin Vondra
 * All Rights Reserved.
 * DESCRIPTION
 */

namespace Leolos;

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
		$this->METHOD = isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : "GET";
		if (array_key_exists("REDIRECT_URL", $_SERVER)) {
			$this->PATH = $_SERVER["REDIRECT_URL"];
		}
		# create new formfield instance to parse URL fields
		$this->form = new FieldStorage\FormField($this->METHOD);
	}

	/**
	 * setAplicationConfigObject(mixed $config)
	 * hold config object/array
	 * @param mixed $config
	 */
	public function setApplicationConfigObject($config) {
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
