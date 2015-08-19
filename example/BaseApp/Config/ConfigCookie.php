<?php

namespace BaseApp\Config;


class ConfigCookie {

    public $domain;
    public $path;
    public $name;
    public $day;

    public function __construct(&$parser, $sectionName) {

        $this->domain = $parser->get($sectionName, "Domain");
        $this->name = $parser->get($sectionName, "Name");
        $this->path = $parser->get($sectionName, "Path");
        $this->day = $parser->get($sectionName, "Day");
    }
}
