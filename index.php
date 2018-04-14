<?php
require 'src/model/Autoloader.class.php';
Autoloader::register();

$router = new Router();
$router->analyse($_REQUEST);
