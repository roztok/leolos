<?php
/*
*
* Copyright (c) 2013, Martin Vondra.
* All Rights Reserved.
*
* DESCRIPTION
* XML-RPC server module
* using built-in xml-rpc server module via php5-xmlrpc
* http://xmlrpc-epi.sourceforge.net/main.php?t=php_api
*
* @author Martin Vondra <martin.vondra@email.cz>
*
*
*
* Usage:
*	$server = new XMLRPCServer();
*	$server->registerMethod("mix.test", "test");
*	$server->registerMethod("main.help", "help");
*	$server->callMethod();
*	$server->destroy();
*/

namespace Leolos\XMLRPCServer;


class XMLRPCServerConfig {
    private $m_encoding;
    private $m_helpFile;

    public function __construct(& $parser=Null) {
        if ($parser) {
            $this->setEncoding($parser->get("xmlrpcserver", "Encoding"));
            $this->setHelpFile($parser->get("xmlrpcserver", "HelpFile"));
        }
    }

    public function setEncoding($encoding) { $this->m_encoding = $encoding; }
    public function getEncoding() { return $this->m_encoding; }
    public function setHelpFile($helpFile) { $this->m_helpFile = $helpFile; }
    public function getHelpFile($helpFile) { return $this->m_helpFile; }
}


class XMLRPCServer {
	private $m_serverRes;
	private $m_encoding;


	/**
	* void __constructor(options)
	* @param options array with server options
	*				- encoding => utf-8
	*				- helpFile => <path to xml help file>
	*
	*/
	public function __construct($options = array()) {
		/* init default values */
		$this->encoding = "utf-8";

		/* create new server */
		$this->m_serverRes = xmlrpc_server_create();

		/* set options */
		if(isset($options['encoding'])) $this->encoding = $options["encoding"];
		if(isset($options['helpFile'])) {
			error_log($options['helpFile']);
			$xml = file_get_contents($options['helpFile']);
			$arr = xmlrpc_parse_method_descriptions($xml);
			xmlrpc_server_add_introspection_data($this->m_serverRes, $arr);
		}
	}


	/**
	* void registerMethod(methodName, callback)
	* add method to server interface
	* @param methodName string method name at the server interface
	* @param callback	mixed name of handler - php function
	*
	* example: 	->registerMethod("user.create", "user_create")
	*			-- object's method, $user is an instance of a class, use &!
	*			->registerMethod("user.create",  array(&$user, "create"))
	*/
	public function registerMethod($methodName, $callback) {
		xmlrpc_server_register_method($this->m_serverRes, $methodName, $callback);
	}


	/**
	*
	*
	*/
	public function callMethod() {
		$response = xmlrpc_server_call_method($this->m_serverRes,
						file_get_contents('php://input','r'), '',
						array(
						"output_type" => "xml",
						"verbosity" => "pretty",
						"escaping" => array("markup", "non-ascii", "non-print"),
						"version" => "xmlrpc",
						"encoding" => $this->encoding));
		//error_log($response);
		header('Content-Type: text/xml');
		print $response;
	}


	public function destroy() {
		xmlrpc_server_destroy($this->serverRes);
	}
}
