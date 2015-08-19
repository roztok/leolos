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
