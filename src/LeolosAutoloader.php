<?php
/*
 *
 * Copyright (c) 2015, Martin Vondra.
 * All Rights Reserved.
 *
 * DESCRIPTION
 *
 * @author Martin Vondra <martin.vondra@email.cz>
 */

class LeolosAutoloader {

	private $loadedModules;
    private $includePath;
	private $debug;

	public function __construct($includePath = "") {
		$this->debug = False;
        $this->includePath = $includePath;
		spl_autoload_register(array($this, 'load'));
	}

	public function enableDebug() {
		$this->debug = True;
	}

	public function disableDebug() {
		$this->debug = False;
	}

	public function logModuleImport($className) {
		echo "Trying to load $className \n";
	}

	private function load($className) {
		if ($this->debug) {
			$this->logModuleImport($className);
		}

		$rf = str_replace("\\", "/", $className).".php";

		require_once($this->includePath.$rf);
	}
}

