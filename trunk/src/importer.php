<?php
/*
 * 
 * Copyright (c) 2012, Martin Vondra
 * All Rights Reserved.
 * DESCRIPTION
 */
 
 
namespace Leolos;
 
 /**
* Importer
* Import aplication modules
* Used in publisher to load modules in dispatchTable
*/
class Importer {

    static function import($moduleList) {
        foreach ($moduleList as $module) {
            # TODO: logging modules
            include "$module.php";
        }
    }
}
