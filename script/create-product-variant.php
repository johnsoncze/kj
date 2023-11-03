<?php

/**
 * Script for create product variants.
 */

declare(strict_types = 1);

$loggerNamespace = 'product-copy-for-parameter-group';

require_once __DIR__ . '/baseScript.php';

/**
 * Settings
 * @var $productsForCopy array [productId => [parameterId => new code],..]
 * @var $parameterGroup int
 * @var $excludeParameterId int[] parameter id for which can be not created product variant
 */
$parameterGroup = 11;
$productsForCopy = [52];
$excludeParameterId = [35];

/**
 * Repositories
 * @var $productAdditionalPhotoRepo \App\Product\AdditionalPhoto\ProductAdditionalPhotoRepository
 * @var $productRepo \App\Product\ProductRepository
 * @var $productTranslationRepo \App\Product\Translation\ProductTranslationRepository
 * @var $groupParameterRepo \App\ProductParameter\ProductParameterRepository
 * @var $productRelated \App\Product\Related\RelatedRepository
 * @var $productParameterRepo \App\Product\Parameter\ProductParameterRepository
 * @var $parameterRepo \App\ProductParameter\ProductParameterRepository
 */
$productAdditionalPhotoRepo = $container->getByType(\App\Product\AdditionalPhoto\ProductAdditionalPhotoRepository::class);
$productRepo = $container->getByType(\App\Product\ProductRepository::class);
$productTranslationRepo = $container->getByType(\App\Product\Translation\ProductTranslationRepository::class);
$productParameterRepo = $container->getByType(\App\Product\Parameter\ProductParameterRepository::class);
$productRelated = $container->getByType(\App\Product\Related\RelatedRepository::class);
$productParameterRepo = $container->getByType(\App\Product\Parameter\ProductParameterRepository::class);
$parameterRepo = $container->getByType(\App\ProductParameter\ProductParameterRepository::class);

/**
 * Facades
 * @var $productAdditionalPhotoFacade \App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacade
 * @var $productFacade \App\Product\ProductSaveFacade
 * @var $productParameterFacade \App\Product\Parameter\ParameterStorageFacade
 * @var $productRelatedFacade \App\Product\Related\RelatedFacade
 * @var $productTranslationFacade \App\Product\Translation\ProductTranslationSaveFacade
 * @var $productVariantFacade \App\Product\Variant\VariantStorageFacade
 * @var $productVariantRepo \App\Product\Variant\VariantRepository
 */
$productAdditionalPhotoFacade = $container->getByType(\App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeFactory::class)->create();
$productFacade = $container->getByType(\App\Product\ProductSaveFacadeFactory::class)->create();
$productParameterFacade = $container->getByType(\App\Product\Parameter\ParameterStorageFacadeFactory::class)->create();
$productRelatedFacade = $container->getByType(\App\Product\Related\RelatedFacadeFactory::class)->create();
$productTranslationFacade = $container->getByType(\App\Product\Translation\ProductTranslationSaveFacadeFactory::class)->create();
$productVariantFacade = $container->getByType(\App\Product\Variant\VariantStorageFacadeFactory::class)->create();
$productVariantRepo = $container->getByType(\App\Product\Variant\VariantRepository::class);

$errorOnSave = function (string $message, \App\Product\Product $product, App\ProductParameter\ProductParameterEntity $parameter) {
	return sprintf('Nastala chyba při ukládání u produktu %d s parametrem %d. Chyba: %s', $product->getId(), $parameter->getId(), $message);
};

try {
	$products = $productRepo->getByMoreId($productsForCopy);
	$parameters = $parameterRepo->findByMoreGroupId([$parameterGroup]);
	$parameterId = $parameters ? \App\Helpers\Entities::getProperty($parameters, 'id') : [];

	/** @var $product \App\Product\Product */
	foreach ($products as $product) {

		if ($product->getType() !== \App\Product\Product::DEFAULT_TYPE) {
			$message = sprintf('Product with id %d must be type %s. Type %s given.', $product->getId(), \App\Product\Product::DEFAULT_TYPE, $product->getType());
			echo $textLine($message);
		}

		if ($product->isCompleted() !== TRUE) {
			$message = sprintf('Product with id %d must be completed.', $product->getId());
			echo $textLine($message);
		}

		$productAdditionalPhotos = $productAdditionalPhotoRepo->findByProductId($product->getId());
		$productParameters = $productParameterRepo->findByProductId($product->getId());
		$productParameterId = $productParameters ? \App\Helpers\Entities::getProperty($productParameters, 'parameterId') : [];
		$productRelatedProducts = $productRelated->findByProductId($product->getId());

		foreach ($parameters as $parameter) {

			if (in_array($parameter->getId(), $excludeParameterId)) {
				$message = sprintf('Skip parameter with id %d for product with id %d because parameter is excluded.', $parameter->getId(), $product->getId());
				echo $textLine($message);
				continue;
			}

			if (in_array($parameter->getId(), $productParameterId)) {
				$message = sprintf('Skip parameter with id %d for product with id %d.', $parameter->getId(), $product->getId());
				echo $textLine($message);
				continue;
			}

			try {
				$database->beginTransaction();

				//product entity
				$productCode = preg_replace('/-(\d+)-/', sprintf('-%s-', $parameter->getTranslation()->getValue()), $product->getCode());
				$copiedProduct = $productFacade->saveNew($productCode, NULL, $product->getStockState(), $product->getEmptyStockState(), 0, (float)$product->getPrice(), (float)$product->getVat(), \App\Product\Product::PUBLISH, NULL, (bool)$product->getSaleOnline(), TRUE, NULL, $product->getType());

				//product translations
				foreach ($product->getTranslations() as $productTranslation) {
					$productTranslation = $productTranslationFacade->saveNew($copiedProduct->getId(), $productTranslation->getLanguageId(), $productTranslation->getName(), $productTranslation->getDescription(), NULL, $productTranslation->getTitleSeo(), $productTranslation->getDescriptionSeo());
					$copiedProduct->addTranslation($productTranslation);
				}

				//product feed data
				if ($product->getGoogleMerchantCategory() && $product->getGoogleMerchantBrandText() && $product->getZboziCzCategory() && $product->getHeurekaCategory()) {
					$copiedProduct = $productFacade->saveProductSearchEngines($copiedProduct->getId(), $product->getGoogleMerchantCategory(), $product->getGoogleMerchantBrandText(), $product->getZboziCzCategory(), $product->getHeurekaCategory());
				}

				//product parameters
				$productParameterFacade->add($copiedProduct->getId(), $parameter->getId());
				foreach ($productParameters as $productParameter) {
					if (!in_array($productParameter->getParameterId(), $parameterId)) {
						$productParameterFacade->add($copiedProduct->getId(), $productParameter->getParameterId());
					}
				}

				//save copied product as variant
				$productVariantFacade->add($product->getId(), $copiedProduct->getId(), $parameterGroup);

				//product related products
				foreach ($productRelatedProducts as $productRelatedProduct) {
					if (($pv1 = $productVariantRepo->findOneByProductVariantId($copiedProduct->getId())) || ($pv2 = $productVariantRepo->findOneByProductVariantId($productRelatedProduct->getRelatedProductId()))) {
						unset($pv1, $pv2);
						continue;
					}
					$productRelatedFacade->add($copiedProduct->getId(), $productRelatedProduct->getRelatedProductId(), $productRelatedProduct->getType());
				}

				//main photo
				$baseDir = __DIR__ . '/../www/upload/';
				$copiedProductDir = $baseDir . $copiedProduct->getUploadFolder();
				if ($product->getPhoto()) {
					$productDirectory = realpath($baseDir . $product->getUploadFolder());
					$newPhoto = $productDirectory . DIRECTORY_SEPARATOR . time() . '-' . $product->getPhoto();
					copy($productDirectory . DIRECTORY_SEPARATOR . $product->getPhoto(), $newPhoto);
					$fileUpload = new \Nette\Http\FileUpload([
						'name' => $product->getPhoto(),
						'type' => 'image/png',
						'size' => 999,
						'tmp_name' => $newPhoto,
						'error' => 0
					]);
					$productFacade->savePhoto($copiedProduct, $fileUpload, $copiedProductDir);
				}

				//additional photos
				foreach ($productAdditionalPhotos as $productAdditionalPhoto) {
					$productDirectory = realpath($baseDir . $product->getUploadFolder());
					$newPhoto =  $productDirectory . DIRECTORY_SEPARATOR . time() . '-' . $productAdditionalPhoto->getFileName();
					copy($productDirectory . DIRECTORY_SEPARATOR . $productAdditionalPhoto->getFileName(),$newPhoto);
					$fileUpload = new \Nette\Http\FileUpload([
						'name' => $productAdditionalPhoto->getFileName(),
						'type' => 'image/png',
						'size' => 999,
						'tmp_name' => $newPhoto,
						'error' => 0
					]);
					$productAdditionalPhotoFacade->add($copiedProduct, [$fileUpload], $copiedProductDir);
				}

				$database->commit();

				$message = sprintf('Byla uložena varianta s id %d pro produkt s id %d s parameter id %d.', $copiedProduct->getId(), $product->getId(), $parameter->getId());
				echo $textLine($message);
			} catch (\App\Product\ProductSaveFacadeException $exception) {
				$database->rollBack();
				echo $errorOnSave($exception->getMessage(), $product, $parameter);
			} catch (\App\Product\Translation\ProductTranslationSaveFacadeException $exception) {
				$database->rollBack();
				echo $errorOnSave($exception->getMessage(), $product, $parameter);
			} catch (\App\Product\Parameter\ParameterStorageException $exception) {
				$database->rollBack();
				echo $errorOnSave($exception->getMessage(), $product, $parameter);
			} catch (\App\Product\Related\RelatedFacadeException $exception) {
				$database->rollBack();
				echo $errorOnSave($exception->getMessage(), $product, $parameter);
			} catch (\App\Product\Variant\VariantStorageFacadeException $exception) {
				$database->rollBack();
				echo $errorOnSave($exception->getMessage(), $product, $parameter);
			}
		}
	}

} catch (\App\NotFoundException $exception) {
	echo $textLine($exception->getMessage());
}