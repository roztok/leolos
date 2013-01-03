<?php
/*
 * 
 * Copyright (c) 2012, Martin Vondra
 * All Rights Reserved.
 * DESCRIPTION
 */

namespace Leolos\Fieldstorage;


/** 
* FormField
* @author Martin Vondra <martin.vondra@email.cz>
*/
class FormField {
	
	private $method; /* http reguest method: GET, POST - use $_GET or $_POST */
	private $field;  /* pointer to right storage by method type */


	/**
	* void __constructor(string $method)
	* Create object to parse fields from URI using superglobal _GET, _POST 
	* 
	* @param string $method http method; GET, POST
	* @return boolean
	*/
	public function __construct($method) {
		$this->setMethod($method);
	}


	/** 
	* boolean setMethod(string $method)
	* protected
	* @param mixed $method
	* @return boolean
	*/
	protected function setMethod($method) {
		if ($method == "GET")
			$this->field = & $_GET;
		elseif ($method == "POST")
			$this->field = & $_POST;
		else
			return False;

		return True;
	}


	/**
	* mixed function get(string $key[, string $type[, mixed $default]])
	* Parse field from URI, set data type, get default value whan doesn't exists
	* 
	* @see FormFiled::settype()
	* @param string $key name of field
	* @param string $type output data type
	* @param mixed $default return Null if $key not find in fields
	* @return mixed output value
	*/
	public function get($key, $type="str", $default=Null) {

		/* field not exists, return default */
		if (!array_key_exists($key, $this->field)) {
			return $default;
		}

		$value = & $this->field[$key];
		return $this->settype($value, $type);
	}


	/**
	* array function getList(string $key[, string $type[, mixed $default]])
	* Parse list of fiels from URI alike FormField::get()
	* 
	* @param string $key name of field
	* @param mixed $type output data type of items in list
	* @param mixed $default return empty array if $key not find in fields
	* @return array output list
	*/
	public function getList($key, $type="str", $default=Null) {
		
		/* filed not exists, return default */
		if (!array_key_exists($key, $this->field)) {
			return ($default == Null) ? array() : $default;
		}

		$value = & $this->field[$key];
		if ($type == Null) {
			return $value;
		}

		if (!is_array($value))
			return array($this->settype($value, $type));

		$out = array();
		foreach ($value as $k => $v) {
			$out[$k] = $this->settype($v, $type);
		}
		return $out;
	}


	/**
	* mixed function settype(mixed $value, string $type)
	* Convert value to chosen data type
	* 
	* @param mixed $value
	* @param string $type output data type
	* one of 'str','int','bool','float', native is 'str'
	* @return mixed
	*/
	static function settype($value, $type) {
		switch ($type) {
			case "str" : return (string)$value;
			break;
			case "int" : return (int)$value;
			break;
			case "bool" : return (bool)$value;
			break;
			case "float" : return (float)$value;
			break;
		}
	}


	/** 
	* FormFile function getFile(string $key)
	* 
	* @param mixed $key file identification
	* @return FormFile
	*/
	public function getFile($key) {
		if (!array_key_exists($key, $_FILES)) {
			return Null;
		}
		return new FormFile($_FILES[$key]);
	}
}


/**
* FormFile
* @author Martin Vondra <martin.vondra@email.cz>
*/
class FormFile {

	public $name;
	public $type;
	private $tmpName;
	private $error;
	public $size;
	public $buffer = "";


	/**
	* __construct(array $attr)
	* Set file attributes, read file to buffer
	* 
	* @param array $attr File attributes: name, type, tmp_name, error, size
	* TODO: store file etc.
	*/
	public function __construct($attr) {
		$this->name = $attr["name"];
		$this->type = $attr["type"];
		$this->tmpName = $attr["tmp_name"];
		$this->error = $attr["error"];
		$this->size = $attr["size"];

		if(file_exists($this->tmpName)) {
			$file = fopen($this->tmpName, "r");
			$this->buffer = fread($file, filesize($this->tmpName));
			fclose($file);
		}
	}
}
