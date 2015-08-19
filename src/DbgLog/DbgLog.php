<?php
/*
* Copyright (c) 2015, Martin Vondra.
* All Rights Reserved.
*
* DESCRIPTION
* log module
*
* @author Martin Vondra <martin.vondra@email.cz>
*/

namespace Leolos\DbgLog;

class DbgLog {

    private $logFile;
    private $logMask;
    private $logMessages;

    const DEBUG1 = 'D1';
    const DEBUG2 = 'D2';
    const DEBUG3 = 'D3';
    const DEBUG4 = 'D4';

    const INFO1 = 'I1';
    const INFO2 = 'I2';
    const INFO3 = 'I3';
    const INFO4 = 'I4';

    const ERR1 = 'E1';
    const ERR2 = 'E2';
    const ERR3 = 'E3';
    const ERR4 = 'E4';

    const FATAL1 = 'F1';
    const FATAL2 = 'F2';
    const FATAL3 = 'F3';
    const FATAL4 = 'F4';

    public function __construct(& $parser = null) {
        $this->logMessages = array();

        if ($parser) {
            $this->setLogMask($parser->get("dbglog", "LogMask"));
            $this->logFile = $parser->get("dbglog", "LogFile");
        } else {
            $this->logFile = null;
            $this->setLogMask("all");
        }
        //print_r($this->logMask);
    }

    public function setLogFile($filename) {
        $this->logFile = $filename;
    }

    public function setLogMask($logMask) {

        if (strtolower($logMask) == "all") {
            $this->logMask = array("F" => 1, "E" => 1, "I" => 1, "D" => 1);
        } else {
            $this->logMask = array();
            $arr = explode("|", $logMask);
            foreach ( $arr as $level) {
                $level = strtoupper($level);
                $this->logMask[substr($level, 0, 1)] = substr($level, 1, 1);
            }
        }
    }

    public function log($logLevel, $message) {
        $bgtrc = debug_backtrace();

        $this->logMessages[] = array("level" => $logLevel, "message" => $message, "backtrace" => $bgtrc);
    }

    public function logToFile() {
        $file = new \SplFileObject($this->logFile, 'a+');

        foreach ($this->logMessages as $line) {

            // logmask check - write log to file
            if ($this->checkMaskLevel($line["level"])) {
                $trc = isset($line["backtrace"][1]) ? $line["backtrace"][1] : $line["backtrace"][0];
                if (!isset($trc["line"])) { 
                    $trc["line"] = "??";
                }

                if (!isset($trc["file"])) {
                    $trc["file"] = "??";
                }
                $file->fwrite("[" . date("c") . "::" . gethostname() . "] " . $line["level"] . " - " .  $line["message"] .
                    " [" . basename($trc["file"]) . "::" . $trc["line"] . "]\n");
            }
        }

        $file->fflush();
        $file = null;

        $this->logMessages = array();
    }

    protected function checkMaskLevel($level) {

        if (isset($this->logMask[substr($level, 0, 1)]) && substr($level, 1, 1) >= $this->logMask[substr($level, 0, 1)]) {
            return True;
        } else {
            return False;
        }
    }

    public function __destruct() {
        if ($this->logFile) {
            $this->logToFile();
        }
    }
}
