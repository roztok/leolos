<?php
#
# Copyright (c) 2011, Ceska televize
# All Rights Reserved.
# 
# $Id: $
#
# DESCRIPTION
# XML-RPC proxy - client
# XML-RPC client pro komunikaci se vzdalenym serverem pomoci XML-RPC
# 
# AUTHOR
# Martin Vondra <martin.vondra@ceskatelevize.cz>
#
# HISTORY
# 2011-05-17 (martin.vondra)
#
# Usage:
# $proxy = new XMLRPCProxy("http://localhost:8001/ct24/test");
# //zavolame metodu 'mix.test(param1, param2)'
# $res = $proxy->mix->test("aaa", "bbbb"); //parametry tak, jak jsou na rozhrani
#

/**
*
* XMLRPC proxy
* Trida pro komunikaci s XML-RPC serverem.
*/
class XMLRPCProxy {
	const ERR_SERVER_BAD_RESPONSE = -1;
	
	protected $url;
	protected $encoding;
	public $res;
	public $lastMethodName;

	/**
	* __constructor(string url[, string encoding])
	* Konstruktor
	* @param url url adresa rpc-serveru
	* @param encoding znakova sada pro komunikaci - POZOR, doporucuji ponechat,
	* ikdyz data budou v utf-8, dochazelo ke zdvojene  konverzi, nutno otestovat
	*/
	public function __construct($url, $encoding = 'iso-8859-1') {
		$this->url = $url;
		$this->encoding = $encoding;
	}

	/**
	* _RPCClient(string method, array params)
	* zavola metodu na serveru
	* @param method nazev metody na serveru
	* @param params seznam vstupnich parametru metody
	* @throw XMLRPCFault pri protokolarni chybe -> odpoved ze serveru
	*/
	private function _RPCClient($method, $params) {
		$this->lastMethodName = $method;
		$context = stream_context_create(array('http' => array(
			'method' => "POST",
			'header' => "Content-Type: text/xml",
			'content' => xmlrpc_encode_request($method, $params, 
				array('encoding' => $this->encoding))
		)));
		$textResponse = file_get_contents($this->url, false, $context);
		$this->res = xmlrpc_decode($textResponse, $this->encoding);
		if(!is_array($this->res)){
			throw new XMLRPCFault('Error in response', self::ERR_SERVER_BAD_RESPONSE);
		}
		elseif (xmlrpc_is_fault($this->res)){
			throw new XMLRPCFault($this->res['faultString'], $this->res['faultCode']);
		}
		return $this->res;
	}

	/**
	* array call(string method, array params)
	* zavola metodu na serveru
	* @param method nazev metody na serveru
	* @param params seznam vstupnich parametru metody
	*/
	public function call($method, $params) {
		return $this->_RPCClient($method, $params);
	}

	/**
	*
	* @see call
	*/
	public function __call($name, $arguments) {
		return $this->_RPCClient($name, $arguments);
	}

	/**
	*
	* pretizeni __get -> vytvoreni pseudoobjektu...
	*/
	public function __get($name) {
		$this->$name = new XMLRPCAlias($this, $name);
		return $this->$name;
	}
}


/**
*
* XMLRPCDateTime
* rozsireni na praci s xmlrpc datetime
* @link http://www.php.net/manual/en/class.datetime.php
*
*/
class XMLRPCDateTime extends DateTime {

	/**
	* Konstruktor
	* vstupni parametr bud xmlrpc datetime nebo viz. doc k DateTime class v php
	*/
	public function __construct($input = "now") {
		if (xmlrpc_get_type($input) == 'datetime') {
			parent::__construct($input->scalar);
		} else {
			parent::__construct($input);
		}
	}

	/**
	*
	* Vraci xmlrpc datetime objekt
	*/
	public function getXMLRPCDateTime() {
		$date = $this->format('Y-m-d\TH:i:s');
		xmlrpc_set_type($date, "datetime");
		return $date;
	}

	public function __toString() {
		return $this->format('Y-m-d H:i:s');
	}
}


/**
*
* Simulace volani na serveru
* pretizeni magic method ziskame syntaxy pro valani rpc metody,
* jako by to byl lokalni objekt s metodami
* Podporuje x vnoreni napr: $a->b->c->d->e->f()
*/
class XMLRPCAlias {
	private $_proxy;
	private $_name;
	public $res;

	public function __construct($proxy, $name) {
		$this->_proxy = $proxy;
		$this->_name = $name;
	}

	public function __get($name) {
		$rpcMethodPath = $this->_name.".".$name;
		$this->$name = new XMLRPCAlias($this->_proxy, $rpcMethodPath);
		return $this->$name;
	}

	public function __call($method, $arguments) {
		$methodName = $this->_name.".".$method;
		return $this->_proxy->call($methodName, $arguments);
	}
}


/**
*
* Obecna vyjimka
*/
class RPCError extends ErrorException {

	public function __construct($message, $code) {
		$this->message = $message;
		$this->code = $code;
	}
	
	/*public function handleRequest($publisher) {
		return
	}*/

	public function __toString() {
		 return '<'.get_class($this).'::['.$this->code.']:'.$this->message.'>';
	}
}


/**
*
* XMLRPCFault
* indikuje chybu volani na urovni protokolu - datovy typ, pocet parametru atd...
*/
class XMLRPCFault extends RPCError {
	public function __construct($message, $code) {
		parent::__construct($message, $code);
	}
}


function test() {
	$proxy = new XMLRPCProxy("http://localhost:8001/ct24/test");
	print_r($proxy->mix->test("aaa", "bbbb"));
}