<?php

namespace BaseApp;


class Application {

    protected $language;
    protected $teng;
    protected $db;

    private $config;

    public function __construct() {
        $this->language = Null;
        $this->teng = Null;
        $this->db = Null;
    }

    /**
     * @param $request
     */
    public function init(&$request) {

        $this->config = &$request->config;
        $this->form = &$request->form;

        if (!$this->checkLanguageChange($request)) {
            $this->parseLanguage($request);
        }

        $request->appInit = &$this;
    }

    /**
     * @param $request
     */
    protected function parseLanguage() {

        $languageCookie = new Utils\LanguageCookie($this->config->languageCookie);

        $this->language = $languageCookie->get();

        if (!$this->language) {

            /* get user language */
            $this->language = Utils\Language::getPreferedLanguage($this->config->control->availableLanguages,
                $this->config->control->defaultLanguage);

        }
    }

    private function checkLanguageChange(&$request) {
        $lang = strtolower($request->form->get("lang", "str"));

        if ($lang) {
            $this->setLanguage($lang);
            $this->setLanguageCookie();
            return True;
        } else {
            return False;
        }
    }

    public function setLanguageCookie() {
        $languageCookie = new Utils\LanguageCookie($this->config->languageCookie);
        $languageCookie->set($this->language);
    }

    public function getLanguage() {
        if($this->language === Null) {
            $this->parseLanguage();
        }
        return $this->language;
    }

    public function setLanguage($language) {
        if(in_array($language, $this->config->control->availableLanguages)){
            $this->language = $language;
        }
    }

    /**
     * @param array $dataRoot
     * @return \Leolos\Teng\Teng
     */
    public function getTeng($dataRoot = array()) {
        if (!$this->teng) {
            $dataRoot["language"] = $this->getLanguage();

            //$dataRoot["languageCapISO"] = $this->getLanguage() == "en" ? "GB" : strtoupper($this->getLanguage());

            $dataRoot["URI_PATH"] = isset($_SERVER["REDIRECT_URL"]) ? $_SERVER["REDIRECT_URL"] : "/";
            $this->teng = new \Leolos\Teng\Teng($this->config->template);
            $this->teng->tengInit($dataRoot);

            /* add available languages to template*/
            $fragment = $this->teng->addFragment(Null, "availableLanguages", array());

            foreach ($this->config->control->availableLanguages AS $lang) {

                $this->teng->addFragment($fragment, "language", array("lang" => $lang));
            }
        }
        return $this->teng;
    }

    /**
     *
     */
    public function getDb() {
        if (!$this->db) {
            $this->db = new \Leolos\MysqlDb\MysqlDb($this->config->mysql);
            $this->db->connect();
        }
        return $this->db;
    }
}
