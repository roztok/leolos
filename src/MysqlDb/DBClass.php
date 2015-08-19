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

abstract class DBClass {

	protected $m_id;
	protected $isDirty;
	protected $sqlConn;

	public function __construct(& $sqlConn, $id = 0) {
		$this->isDirty = False;
		$this->sqlConn = $sqlConn;

		if($id) {
			$this->m_id = $id;
			$this->load();
		}
	}

	abstract public function load($row = Null);
	abstract public function save();

	public function membersToArray() {
		$arr = array();
		foreach(get_object_vars($this) AS $key=>$var) {
			$varPfx = substr($key, 0, 2);
			if ($varPfx == "m_") {
				$arr[substr($key, 2)] = $var;
			}
		}
		return $arr;
	}

	public function set($name, $value) {
		if($this->$name != $value) {
			$this->$name = $value;
			$this->isDirty = True;
		}
	}

	public function toTeng($teng, $res, $fragmentName = Null) {
		if($fragmentName === Null) {
			$fragmentName =  strtolower(get_class($this));
		}
		return $teng->addFragment($res,$fragmentName, $this->membersToArray());
	}

	public function __get($param) {
		$this->load();
		return $this->$param;
	}
}

