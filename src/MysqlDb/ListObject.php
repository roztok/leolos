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

class ListObject {

	protected $sqlConn;
	public $list;
	protected $filter;
	protected $join;
	protected $order;
	protected $limit;
	protected $itemClassName;
	protected $dbTableName;

	public function __construct(& $sqlConn) {
		$this->sqlConn = $sqlConn;
		$this->list = array();
		$this->filter = array();
		$this->join = "";
		$this->limit = "";
		$this->order = "";
		$this->direction = "";
	}

	public function setFilter($filter) { $this->filter = $filter; }
	public function setOrder($order) { $this->order = $order; }
	public function setDirection($direction) { $this->direction = $direction; }
	public function setLimit($limit) { $this->limit = $limit; }
	public function setJoin($join) { $this->join = $join; }

	protected function getWhere() {
		$where = "";
		if (count($this->filter)) {
			$where = "WHERE ";
			foreach($this->filter AS $key=>$value) {
				$where .= $this->sqlConn->renderQuery("$key=%s", $value);
			}
		}
		return $where;
	}

	protected function getLimit() {
		if( $this->limit ) {
			return "LIMIT ".$this->limit;
		} else {
			return "";
		}
	}

	protected function getOrder() {
		if( $this->order ) {
			return "ORDER BY ".$this->order." ".$this->direction;
		} else {
			return "";
		}
	}

	public function load() {
		$this->sqlConn->begin();
		$sql = "SELECT * FROM `".$this->dbTableName."` ".$this->join." ".$this->getWhere();
		$sql .= " ".$this->getOrder()." ".$this->getLimit();
		$res = $this->sqlConn->execute($sql);
		while ($row = $res->fetch_object()) {
			$item = new $this->itemClassName($this->sqlConn);
			$item->load($row);
			$this->list[] = $item;
		}
	}

	/**
	 * count(*) use indexes, so could be faster than SQL_CALC_FOUND_ROWS
	 */
	public function getCount() {
		$this->sqlConn->begin();
		$res = $this->sqlConn->execute("SELECT count(*) AS count FROM `".$this->dbTableName.
				"` ".$this->join." ".$this->getWhere());
		$row = $res->fetch_object();
		return $row->count;
	}

	public function toTeng($teng, $res) {
		$res = $teng->addFragment($res, strtolower(get_class($this)), array());
		foreach($this->list AS $item) {
			$item->toTeng($teng, $res);
		}

	}

	public function toHtmlSelectTeng($teng, $res, $selected) {
		$list = array();
		foreach($this->list AS $item) {
			$list[$item->getId()] = $item->getName();
		}
		$teng->addHTMLSelect($res, $this->selectName, $list, $selected);
	}
}
