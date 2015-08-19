<?php

namespace BaseApp\Config;


class ConfigLanguageCookie extends ConfigCookie {

    public function __construct(&$parser) {
        parent::__construct($parser, "cookie::language");
    }
}
