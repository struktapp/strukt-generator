<?php

error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

$loader = require 'vendor/autoload.php';
$loader->add('Strukt', __DIR__."/src/");
// $loader->add('Strukt', __DIR__."/../strukt-fs/src");
// $loader->add('Strukt', __DIR__."/../strukt-commons/src");
$loader->add('Payroll', __DIR__.'/fixtures/app/src/');
$loader->add('App', __DIR__.'/fixtures/lib/');