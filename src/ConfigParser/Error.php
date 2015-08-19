<?php
/*
 * Copyright (c) 2012, Martin Vondra.
 * All Rights Reserved.
 *
 * DESCRIPTION
 * config parser module
 * parsing configuration from standard ini file with section
 * [section]
 * option1 = value1
 * option2 = value2
 *
 * @author Martin Vondra <martin.vondra@email.cz>
 */

namespace Leolos\ConfigParser;

/**
 * Error
 * generic base exception
 * @author Martin Vondra <martin.vondra@email.cz>
 *
 */
class Error extends \ErrorException {

	/**
	 * constructor
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
		return "<".get_class($this).": [".$this->code."]:".$this->message.">\n";
	}
}
