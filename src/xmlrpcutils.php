<?php
/*
*
* Copyright (c) 2013, Martin Vondra.
* All Rights Reserved.
*
* DESCRIPTION
* XML-RPC utils
* 
*
* @author Martin Vondra <martin.vondra@email.cz>
*/

namespace Leolos\XMLRPCServer;

/**
*
* XMLRPCDateTime
* wrapper fo xmlrpc datetime using built-in DateTime object
* @link http://www.php.net/manual/en/class.datetime.php
*
*/
class XMLRPCDateTime extends DateTime {

    /**
    * Konstruktor
    * vstupni parametr bud xmlrpc datetime nebo viz. doc k DateTime class v php
    */
    public function __construct($input = "now") {
        if (xmlrpc_get_type($input) == 'datetime') {
            parent::__construct($input->scalar);
        } else {
            parent::__construct($input);
        }
    }

    /**
    *
    * Vraci xmlrpc datetime objekt
    */
    public function getXMLRPCDateTime() {
        $date = $this->format('Y-m-d\TH:i:s');
        xmlrpc_set_type($date, "datetime");
        return $date;
    }
}
