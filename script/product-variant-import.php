<?php

/**
 * This script import product variants from old database table to the new database table
*/
$loggerNamespace = 'product_variant_v2_import';
/**
 * @param $message
 * @return string
 */
$logMessage = function (string $message) use ($loggerNamespace) : string {
	return sprintf('%s: %s', $loggerNamespace, $message);
};

/**
 * Print text line into console.
 * @param $text string
 * @return string
 */
$textLine = function (string $text) : string {
	return $text . PHP_EOL;
};

/**
 * @var $container \Nette\DI\Container
 * @var $variantStorageFacade \App\Product\Variant\VariantStorageFacade
 * @var $database \Nette\Database\Context
 * @var $logger \Kdyby\Monolog\Logger
 * @var $relatedFacade \App\Product\Related\RelatedFacade
 */
$container = require __DIR__ . '/../app/bootstrap.php';
$variantStorageFacade = $container->getByType(\App\Product\Variant\VariantStorageFacadeFactory::class)->create();
$database = $container->getByType(\Nette\Database\Context::class);
$logger = $container->getByType(\Kdyby\Monolog\Logger::class);
$relatedFacade = $container->getByType(\App\Product\Related\RelatedFacadeFactory::class)->create();

//get master products
$parameterGroups = [3, 5, 11, 14, 15, 39, 40, 44, 59, 60];
$query = 'SELECT * FROM product_variant ORDER BY pv_product_parameter_group_id, pv_id';
$masterProducts = $database->query($query)->fetchAll();

//import selected variants
$imported = 0;
$error = 0;
$parameterGroups = [3, 5, 11, 14, 15, 39, 40, 44, 59, 60];
foreach ($masterProducts as $key => $masterProduct) {
	try {
		$productId = (int)$masterProduct['pv_product_id'];
		$productVariantId = (int)$masterProduct['pv_product_variant_id'];
		$parameterGroupId = (int)$masterProduct['pv_product_parameter_group_id'];
		$logParams = ['productId' => $productId, 'productVariantId' => $productVariantId, 'parameterGroupId' => $parameterGroupId];

		if (in_array($parameterGroupId, $parameterGroups) === FALSE) {
			continue;
		}

		//start message
		$startMessage = $logMessage('Start import product selected variant..');
		$logger->addInfo($startMessage, $logParams);
		echo $textLine($startMessage . ' ' . print_r($logParams, TRUE));

		$variant = $variantStorageFacade->add($productId, $productVariantId, $parameterGroupId);

		//end message
		$endMessage = $logMessage('Imported selected variant.');
		$endParams = array_merge($logParams, [
			'result' => $variant->toArray(),
		]);
		$logger->addInfo($endMessage, $endParams);
		echo $textLine($endMessage . ' ' . print_r($endParams, TRUE));

		$imported++;
	} catch (\App\Product\Variant\VariantStorageFacadeException $exception) {
		$message = $logMessage($exception->getMessage());
		$logger->addNotice($message);
		echo $textLine($message);
		$error++;
	}

	unset($masterProducts[$key]);
}

//finish message
$message = sprintf('Import of selected variants has been finished. Imported: %d, Error: %d', $imported, $error);
$finishMessage = $logMessage($message);
$logger->addInfo($finishMessage);
echo $textLine($finishMessage);

//import image variants
$imported = 0;
$error = 0;
foreach ($masterProducts as $masterProduct) {
	try {
		$productId = (int)$masterProduct['pv_product_id'];
		$productVariantId = (int)$masterProduct['pv_product_variant_id'];
		$logParams = ['productId' => $productId, 'productVariantId' => $productVariantId];

		//start message
		$startMessage = $logMessage('Start import product image variant..');
		$logger->addInfo($startMessage, $logParams);
		echo $textLine($startMessage . ' ' . print_r($logParams, TRUE));

		$variant = $relatedFacade->add($productId, $productVariantId, \App\Product\Related\Related::SIMILAR);

		//end message
		$endMessage = $logMessage('Imported image variant.');
		$endParams = array_merge($logParams, [
			'result' => $variant->toArray(),
		]);
		$logger->addInfo($endMessage, $endParams);
		echo $textLine($endMessage . ' ' . print_r($endParams, TRUE));

		$imported++;
	} catch (\App\Product\Related\RelatedFacadeException $exception) {
		$message = $logMessage($exception->getMessage());
		$logger->addNotice($message);
		echo $textLine($message);
		$error++;
	}
}

//u produktu 947 ten se zkopíroval jako hlavní, přitom už je varianta (?)
//todo doplnit všechny skupiny
//todo doplnit obrázkový varianty
//todo poslat přehled tabulky co s čim je ted varianta
//todo poslat přehled co nemá žádnou variantu + co není žádná varianta
