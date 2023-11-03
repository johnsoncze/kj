<?php

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../dbconnect.php';

//configure environment
Tester\Environment::setup();
date_default_timezone_set("Europe/Prague");

//create temporary directory
define("TEMP_TEST", __DIR__ . "/../../temp/tests");
define("TEMP_DIR", TEMP_TEST . "/" . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
@mkdir(TEMP_DIR, 0777, true);
Tester\Helpers::purge(TEMP_DIR);

$configurator = new \Nette\Configurator();
$configurator->enableDebugger(__DIR__ . '/../../log');
$configurator->setTempDirectory(TEMP_DIR);
$configurator->createRobotLoader()
    ->addDirectory(__DIR__ . '/../../')
    ->register();

$configurator->addConfig(__DIR__ . '/../../app/config/config.neon');
$configurator->addConfig(getDBConfigPath());
$configurator->addConfig(__DIR__ . '/../../app/config/config.services.neon');

$container = $configurator->createContainer();

\App\Facades\MailerFacade::setTestEnvironment(TRUE);

return $container;