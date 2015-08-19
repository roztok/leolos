<?php

namespace BaseApp\Utils;


class Language {

    /**
     * @param $availableLanguages
     * @return mixed
     */
    static function getPreferedLanguage($availableLanguages, $default = "en") {
        $availableLanguages = array_flip($availableLanguages);

        $langs = array();

        preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
            $matches, PREG_SET_ORDER);

        foreach($matches as $match) {

            list($a, $b) = explode('-', $match[1]) + array('', '');
            $value = isset($match[2]) ? (float) $match[2] : 1.0;

            if(isset($availableLanguages[$match[1]])) {
                $langs[$match[1]] = $value;
                continue;
            }

            if(isset($availableLanguages[$a])) {
                $langs[$a] = $value - 0.1;
            }

        }
        arsort($langs);

        $langKeys = array_keys($langs);

        if (isset($langKeys[0])) {
            return $langKeys[0];
        } else {
            return $default;
        }
    }
}
