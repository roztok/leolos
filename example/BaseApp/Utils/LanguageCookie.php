<?php

namespace BaseApp\Utils;


class LanguageCookie {

    private $cookieConfig;

    public function __construct(&$cookieConfig) {
        $this->cookieConfig = $cookieConfig;
    }

    public function set($language) {
        Cookie::set($this->cookieConfig->name, $language, $this->cookieConfig->day,
            $this->cookieConfig->path, $this->cookieConfig->domain);
    }

    public function get() {
        return Cookie::get($this->cookieConfig->name);
    }
}
