<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/dbconnect.php';

$neon = new \Nette\Neon\Neon();
$result = $neon->decode(file_get_contents(getDBConfigPath()));

$conn = new \Nette\Database\Connection($result["database"]["dsn"], $result["database"]["user"], $result["database"]["password"], NULL);
$dbal = new \Nextras\Migrations\Bridges\NetteDatabase\NetteAdapter($conn);
$driver = new \Nextras\Migrations\Drivers\MySqlDriver($dbal);
$controller = new \Nextras\Migrations\Controllers\ConsoleController($driver);

$baseDir = __DIR__ . "/../migrations";
$controller->addGroup('structures', "$baseDir/structures");
$controller->addGroup('basic-data', "$baseDir/basic-data", ['structures']);
$controller->addGroup('dummy-data', "$baseDir/dummy-data", ['basic-data']);
$controller->addExtension('sql', new \Nextras\Migrations\Extensions\SqlHandler($driver));

$controller->run();