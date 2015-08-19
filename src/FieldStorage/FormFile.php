<?php
/*
 *
 * Copyright (c) 2012, Martin Vondra
 * All Rights Reserved.
 * DESCRIPTION
 */

namespace Leolos\Fieldstorage;


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
