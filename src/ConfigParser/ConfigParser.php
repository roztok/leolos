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
 * ConfigParser
 * @author Martin Vondra <martin.vondra@email.cz>
 *
 */
class ConfigParser {

	private $ini;

	/**
	 * @param string $configFile
	 */
	public function __construct($configFile) {

		if (!file_exists($configFile)) {
			throw new ConfigParserError("No such file at '".$configFile."'.", -1);
		}

		$this->ini = parse_ini_file($configFile, True);
		if ($this->ini === False) {
			throw new ConfigParserError("Syntax error or parsing error.", -2);
		}
	}

	/**
	 * string get(string $section, string $option [, mixed $default])
	 * @param string $section
	 * @param string $option
	 * @param mixed $default
	 * @return mixed
	 * @throws NoSectionError, NoOptionError
	 */
	public function get($section, $option, $default=Null) {
		//missing section
		if (!array_key_exists($section, $this->ini)) {
			throw new NoSectionError("Missing section '".$section."'.", -1);
		}
		//missing option in section
		if (!array_key_exists($option, $this->ini[$section])) {
			if ($default !== Null) {
				return $default;
			}
			throw new NoOptionError("Missing option '".$option."'.", -2);
		}
		return $this->ini[$section][$option];
	}

	/**
	 * int getInt(string $section, string $option [, mixed $default])
	 * @param string $section
	 * @param string $option
	 * @param mixed $default
	 * @return int
	 */
	public function getInt($section, $option, $default=Null) {
		return (int)$this->get($section, $option, $default);
	}

	/**
	 * float getFloat(string $section, string $option [, mixed $default])
	 * @param string $section
	 * @param string $option
	 * @param mixed $default
	 * @return float
	 */
	public function getFloat($section, $option, $default=Null) {
		return (float)$this->get($section, $option, $default);
	}

	/**
     * boolean getBool(string $section, string $option [, mixed $default])
	 * @param string $section
	 * @param string $option
	 * @param mixed $default
	 * @return bool
	 */
	public function getBool($section, $option, $default=Null) {
		return (bool)$this->get($section, $option, $default);
	}
}