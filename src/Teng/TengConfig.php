<?php
/*
 *
 * Copyright (c) 2012, Martin Vondra.
 * All Rights Reserved.
 *
 * DESCRIPTION
 * Teng module
 * module for using templating engine Teng
 *
 * @author Martin Vondra <martin.vondra@email.cz>
 */

namespace Leolos\Teng;

class TengConfig {
	protected $m_templatePath;
	protected $m_language;
	protected $m_dictFile;
	protected $m_configFile;
	protected $m_skin;

	public function __construct(& $parser=Null) {
		if($parser) {
			//Leolos\ConfigParser\ConfigParser instance
			$this->setTemplatePath($parser->get("teng", "TemplatePath"));
			$this->setLanguage($parser->get("teng", "DefaultLanguage"));
			$this->setDictFile($parser->get("teng", "DictFile"));
			$this->setConfigFile($parser->get("teng", "ConfFile"));
			$this->setSkin($parser->get("teng", "DefaultSkin"));
		} else {
			//set to default
			$this->m_templatePath='';
			$this->m_language='';
			$this->m_dictFile='';
			$this->m_configFile='';
			$this->m_skin='';
		}
	}

	public function getTemplatePath() { return $this->m_templatePath; }
	public function setTemplatePath($path) { $this->m_templatePath = $path; }
	public function getLanguage() { return $this->m_language; }
	public function setLanguage($lang) { $this->m_language = $lang; }
	public function getDictFile() { return $this->m_dictFile; }
	public function setDictFile($dictFile) { $this->m_dictFile = $dictFile; }
	public function getConfigFile() { return $this->m_configFile; }
	public function setConfigFile($configFile) { $this->m_configFile = $configFile; }
	public function getSkin() { return $this->m_skin; }
	public function setSkin($skin) { $this->m_skin = $skin; }
}
