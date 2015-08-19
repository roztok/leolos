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
 *
 */
interface MysqlDbLogger {

	public function info($msg);
	public function debug($msg);
	public function warning($msg);
	public function error($msg);
}
