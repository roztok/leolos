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


/**
* Teng - class for using teng templating engine
* @link http://teng.sf.net
* @link http://teng/olmik.net
*
*/
class Teng {

	public $teng;
	public $dataRoot;
	private $configFile;
	private $dictFile;
	private $defaultLanguage;
	private $defaultSkin;
	private $templPath;


	/**
	*
	* public __constructor(string config, string dict[,array root])
	* Create obejct for using teng templ. engine
	*
	* @param string $config path to config file
	* @param string $dict path to default dictionary
	* @param array $root rootfragmnet - array with variables to root
	* @return Teng
	*/
	public function __construct(TengConfig $tengConfig) {
		$this->templPath = $tengConfig->getTemplatePath();
		$this->configFile = $tengConfig->getConfigFile();
		$this->dictFile = $tengConfig->getDictFile();
		$this->defaultLanguage = $tengConfig->getLanguage();
		$this->defaultSkin = $tengConfig->getSkin();
	}


	/**
	* public tengInit(array root)
	* Init new teng's data root fragment
	*
	* @param array $root array with variables
	*/
	public function tengInit($root) {
		$this->teng = teng_init($this->templPath);
		$this->createDataRoot($root);
	}

	/**
	* public createDataRoot(array root)
	* Create new root fragment with data in root
	*
	* @param array root array as dictionary
	* @return resource to teng fragment
	*/
	public function createDataRoot($root = '') {
		if (!is_array($root)) {
			$root = array();
		}
		return $this->dataRoot = teng_create_data_root($root);
	}


	/**
	* public addFragment(resource teng, string fragName, array data)
	* Create new teng fragment and fill it with data
	*
	* @param teng fragment resource, for new root fragment use 'Null'
	* @param sring fragName new fragment name
	* @param array data array with values - must use strings as keys
	* @return resource to new teng fragmnet
	*/
	public function addFragment($teng, $fragName, $data) {
		if ($teng === Null) {
			$teng = $this->dataRoot;
		}
		return teng_add_fragment( $teng, $fragName, $data );
	}


	/**
	* public dictLookup(string key, [string dict [, string lang])
	* Looks up the dictionary
	*
	*/
	public function dictLookUp($key, $dictFile = Null, $language = Null) {
		if (!$dictFile) {
			$dictFile = & $this->dictFile;
		}
		if (!$language) {
			$language = & $this->defaultLanguage;
		}
		return teng_dict_lookup($this->teng, $key, $dictFile, $language);
	}


	/**
	* public addArray( resource teng, string fragName, array array [,
	*					 string mykey ] )
	* Similar to addFragment but data is in 2dimension array
	* @see addFragment
	*/
	public function addArray($teng, $fragName, $data, $myKey = 'defkey') {
		if (!$teng) {
			$teng = & $this->dataRoot;
		}

		foreach ($data AS $key => $value) {
			if (!is_array($value)) {
				$value = array($myKey => $value);
			}
			$link = $this->addFragment($teng, $fragName, $value);
		}
		if (isset($link)) {
			return $link;
		} else {
			return $teng;
		}
	}


	/**
	* public addHTMLSelect( resource teng, string fragName, array array [,
	*					  mixed selected ] )
	* Create fragments structure for html select
	*
	* @see addFragment
	* @param teng fragment resource
	* @param string fragName new teng fragment name
	* @param array array with values - must use strings as keys
	* @param mixed selected means which options will be selected
	* @return resource to new teng fragment
	*/
	public function addHTMLSelect($teng, $fragName, $array, $selected = '') {
		if (!$teng) {
			$teng = & $this->dataRoot;
		}
		if (!is_array($selected)) {
			$selected = array($selected);
		}

		$select = $this->addFragment($teng, $fragName, array());
		foreach ($array AS $key => $value) {
			$optionSelected = '';
			if (in_array($key, $selected)) {
				$optionSelected = 'selected';
			}
			$this->addFragment($select, 'option',
				array('value' => $key,
					'text' => $value,
					'selected' => $optionSelected));
		}
		return $select;
	}


	/**
	  * public generatetPage([string tmpl [, string lang [, string skin [,
	  * 		string contentType[, string encoding[,	boolean printPage ]]]]]] )
	  * Generate a page code using template
	  *
	  * @param tmpl template file, default modulename without '.php'
	  * @param lang language for dict
	  * @param skin skin type of template
	  * @param contentType document content-type, default 'text/html'
	  * @param encoding document encoding, default 'utf-8'
	  * @param boolean printPage if false only return page as string
	  * @return page source code
	  */
	public function generatePage($tmpl = '', $lang = '', $skin = '', $encoding = 'utf-8', 
        $contentType = "text/html", $printPage = false) {

		if (!$lang) $lang = & $this->defaultLanguage;
		if (!$skin) $skin = & $this->defaultSkin;

		if ($tmpl == '') {
			$tmpl = str_replace('.php', '.html', basename($_SERVER['PHP_SELF']));
		}

		$page = teng_page_string( $this->teng, $tmpl, $this->dataRoot,
				array('config' => $this->configFile,
						'dict' => $this->dictFile,
						'lang' => $lang,
						'skin' => $skin,
						'content_type' => $contentType,
						'encoding' => $encoding));

		/* release data */
		$this->destroy();

		/* print page source to request */
		if ($printPage) {
			echo $page;
			return ;
		}

		/* return page as a big string */
		return $page;
	}


	/* release data from memory - destroy dataroot */
	private function destroy() {
		return teng_release_data($this->dataRoot);
	}
}
