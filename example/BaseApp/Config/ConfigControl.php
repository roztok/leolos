<?php

namespace BaseApp\Config;


class ConfigControl {
    public $baseURL;
    public $availableLanguages;
    public $defaultLanguage;

    public function __construct(&$parser) {
        $this->baseURL = $parser->get("control","BaseURL");
        $this->availableLanguages = explode(",", $parser->get("control","AvailableLanguages"));
        $this->defaultLanguage = $parser->get("control", "DefaultLanguage");
    }
}

