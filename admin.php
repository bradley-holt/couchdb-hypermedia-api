#!/usr/bin/php -q
<?php
defined('APPLICATION_ROOT')
    || define('APPLICATION_ROOT', realpath(dirname(__FILE__)));
require APPLICATION_ROOT . '/vendor/autoload.php';

use Cilex\Application;
use CouchDbHypermediaApi\Version;

$cilexApp = new Application('CouchDB Hypermedia API', Version::VERSION);
$cilexApp->run();
