<?php


namespace BaseApp\Config;

class Config {
    
    public $control;
    public $dbglog;
    public $languageCookie;
    public $mysql;

    public function __construct() {

        $configFile = getenv('ConfigFile');
        if (!$configFile) {
            $configFile = '/var/www/baseapp.com/conf/baseapp.ini';
        }

        $parser = new \Leolos\ConfigParser\ConfigParser($configFile);

        $this->control = new ConfigControl($parser);
        $this->languageCookie = new ConfigLanguageCookie($parser);
        $this->template = new \Leolos\Teng\TengConfig($parser);
        $this->dbglog = new \Leolos\DbgLog\DbgLog($parser);
        $this->mysql = new \Leolos\MysqlDb\MysqlDbConfig($parser);
    }

}
