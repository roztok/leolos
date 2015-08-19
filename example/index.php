<?php

require_once 'Leolos/LeolosAutoloader.php';


$autoloader = new LeolosAutoloader(__DIR__."/");
//$autoloader->enableDebug();


$publisher = new Leolos\Dispatcher();

/* configuration register */
$publisher->setApplicationConfigObject(new \BaseApp\Config\Config());

/* application init class */
$publisher->setApplicationInitObject(new \BaseApp\Application());


$publisher->addHandler(new Leolos\FunctionHandler("", "BaseApp\homepageScreen", "GET", __DIR__."/BaseApp/homepage", False));

$publisher->handleRequest();

