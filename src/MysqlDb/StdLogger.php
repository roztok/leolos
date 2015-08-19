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
 * standard dummy system logger
 */
class StdLogger implements MysqlDbLogger {
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
