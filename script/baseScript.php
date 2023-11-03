<?php

declare(strict_types = 1);

$container = require __DIR__ . '/../app/bootstrap.php';

/**
 * @var $database \Nette\Database\Context
*/
$database = $container->getByType(\Nette\Database\Context::class);

/**
 * Print text line into console.
 * @param $text string
 * @return string
 */
$textLine = function (string $text) : string {
	return $text . PHP_EOL;
};