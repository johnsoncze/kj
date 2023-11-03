<?php

declare(strict_types = 1);

namespace App\Product\Variant\Copy;

use App\Helpers\Entities;
use App\Product\AdditionalPhoto\PhotoFactory;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoRepository;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeException;
use App\Product\Parameter\ParameterStorageException;
use App\Product\Parameter\ParameterStorageFacade;
use App\Product\Parameter\ParameterStorageFacadeFactory;
use App\Product\Parameter\ProductParameterRepository AS ProductParameterRelatedRepository;
use App\Product\Photo\PhotoManager;
use App\Product\Photo\PhotoManagerException;
use App\Product\Product;
use App\Product\ProductNotFoundException;
use App\Product\ProductRepository;
use App\Product\ProductSaveFacade;
use App\Product\ProductSaveFacadeException;
use App\Product\ProductSaveFacadeFactory;
use App\Product\Translation\ProductTranslationSaveFacade;
use App\Product\Translation\ProductTranslationSaveFacadeException;
use App\Product\Translation\ProductTranslationSaveFacadeFactory;
use App\Product\Variant\VariantStorageFacade;
use App\Product\Variant\VariantStorageFacadeException;
use App\Product\Variant\VariantStorageFacadeFactory;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupNotFoundException;
use App\ProductParameterGroup\ProductParameterGroupRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CopyFacade
{


	/** @var ProductParameterRepository */
	private $groupParameterRepository;

	/** @var ProductSaveFacade */
	private $productFacade;

	/** @var ProductParameterGroupRepository */
	private $parameterGroupRepository;

	/** @var PhotoFactory */
	private $productAdditionalPhotoFactory;

	/** @var ProductAdditionalPhotoRepository */
	private $productAdditionalPhotoRepo;

	/** @var ParameterStorageFacade */
	private $productParameterFacade;

	/** @var ProductParameterRelatedRepository */
	private $productParameterRelatedRepo;

	/** @var PhotoManager */
	private $productPhotoManager;

	/** @var ProductRepository */
	private $productRepo;

	/** @var ProductTranslationSaveFacade */
	private $productTranslationFacade;

	/** @var VariantStorageFacade */
	private $productVariantFacade;



	public function __construct(PhotoManager $photoManager,
								PhotoFactory $photoFactory,
								ProductAdditionalPhotoRepository $productAdditionalPhotoRepo,
								ProductParameterRepository $productParameterRepo,
								ProductParameterGroupRepository $productParameterGroupRepo,
								ProductSaveFacadeFactory $productSaveFacadeFactory,
								ProductParameterRelatedRepository $productParameterRelatedRepo,
								ProductRepository $productRepository,
								ParameterStorageFacadeFactory $parameterStorageFacadeFactory,
								ProductTranslationSaveFacadeFactory $productTranslationSaveFacade,
								VariantStorageFacadeFactory $variantStorageFacade)
	{
		$this->groupParameterRepository = $productParameterRepo;
		$this->productAdditionalPhotoFactory = $photoFactory;
		$this->productAdditionalPhotoRepo = $productAdditionalPhotoRepo;
		$this->productFacade = $productSaveFacadeFactory->create();
		$this->parameterGroupRepository = $productParameterGroupRepo;
		$this->productPhotoManager = $photoManager;
		$this->productParameterFacade = $parameterStorageFacadeFactory->create();
		$this->productParameterRelatedRepo = $productParameterRelatedRepo;
		$this->productRepo = $productRepository;
		$this->productTranslationFacade = $productTranslationSaveFacade->create();
		$this->productVariantFacade = $variantStorageFacade->create();
	}



	/**
	 * @param $productId int
	 * @param $parameterGroupId int
	 * @param $productCodes array [parameterId => productCode,..]
	 * @return int count of saved products
	 * @throws CopyFacadeException
	 */
	public function copyParameterGroup(int $productId, int $parameterGroupId, array $productCodes) : int
	{
		try {
			$sourceProduct = $this->productRepo->getOneById($productId);
			$parameterGroup = $this->parameterGroupRepository->getOneById($parameterGroupId);
			$groupParameters = $this->groupParameterRepository->findByMoreGroupId([$parameterGroup->getId()]);
			$groupParameterId = $groupParameters ? Entities::getProperty($groupParameters, 'id') : [];

			$i = 0;
			foreach ($groupParameters as $groupParameter) {
				$productCode = $productCodes[$groupParameter->getId()] ?? NULL;
				if ($productCode) {
					$targetProduct = $this->saveProduct($sourceProduct, $productCode);
					$targetProduct = $this->saveProductFeedData($sourceProduct, $targetProduct);
					$targetProduct = $this->saveTranslations($sourceProduct, $targetProduct);
					$targetProduct = $this->saveProductParameters($sourceProduct, $targetProduct, $groupParameter, $groupParameterId);
					$targetProduct = $this->saveVariant($sourceProduct, $targetProduct, $parameterGroup);
					$this->savePhotos($sourceProduct, $targetProduct);
					unset($targetProduct);
					$i++;
				}
			}

			return $i;
		} catch (ProductNotFoundException $exception) {
			throw new CopyFacadeException($exception->getMessage());
		} catch (ProductParameterGroupNotFoundException $exception) {
			throw new CopyFacadeException($exception->getMessage());
		} catch (ProductSaveFacadeException $exception) {
			throw new CopyFacadeException($exception->getMessage());
		} catch (ProductTranslationSaveFacadeException $exception) {
			throw new CopyFacadeException($exception->getMessage());
		} catch (ParameterStorageException $exception) {
			throw new CopyFacadeException($exception->getMessage());
		} catch (VariantStorageFacadeException $exception) {
			throw new CopyFacadeException($exception->getMessage());
		} catch (ProductAdditionalPhotoSaveFacadeException $exception) {
			throw new CopyFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param $source Product
	 * @param $code string
	 * @return Product
	 * @throws ProductSaveFacadeException
	 */
	private function saveProduct(Product $source, string $code) : Product
	{
		return $this->productFacade->saveNew($code,
			NULL,
			$source->getStockState(),
			$source->getEmptyStockState(),
			0,
			(float)$source->getPrice(),
			(float)$source->getVat(),
			$source->getState(),
			$source->getNewUntilTo() ? new \DateTime($source->getNewUntilTo()) : NULL,
			NULL,
			NULL,
			NULL,
			NULL,
			(bool)$source->getSaleOnline(),
			$source->isCompleted(),
			$source->getCommentCompleted(),
			$source->getType(),
			$source->isDiscountAllowed());
	}



	/**
	 * @param $source Product
	 * @param $target Product
	 * @return Product target product
	 * @throws ProductTranslationSaveFacadeException
	 */
	private function saveTranslations(Product $source, Product $target) : Product
	{
		$translations = $source->getTranslations();
		foreach ($translations as $productTranslation) {
			$productTranslation = $this->productTranslationFacade->saveNew($target->getId(),
				$productTranslation->getLanguageId(),
				$productTranslation->getName(),
				$productTranslation->getDescription(),
				NULL,
				$productTranslation->getTitleSeo(),
				$productTranslation->getDescriptionSeo(),
				$productTranslation->getShortDescription());
			$target->addTranslation($productTranslation);
		}
		return $target;
	}



	/**
	 * @param $source Product
	 * @param $target Product
	 * @return Product
	 * @throws ProductSaveFacadeException
	 */
	private function saveProductFeedData(Product $source, Product $target) : Product
	{
		if ($source->getGoogleMerchantCategory()
			&& $source->getGoogleMerchantBrandText()
			&& $source->getZboziCzCategory()
			&& $source->getHeurekaCategory()
		) {
			$target = $this->productFacade->saveProductSearchEngines($target->getId(),
				$source->getGoogleMerchantCategory(),
				$source->getGoogleMerchantBrandText(),
				$source->getZboziCzCategory(),
				$source->getHeurekaCategory());
		}
		return $target;
	}



	/**
	 * @param $source Product
	 * @param $target Product
	 * @param $parameter ProductParameterEntity for which is product copied
	 * @param $excludeParameterId array
	 * @return Product
	 * @throws ParameterStorageException
	 */
	private function saveProductParameters(Product $source, Product $target, ProductParameterEntity $parameter, array $excludeParameterId = [])
	{
		$this->productParameterFacade->add($target->getId(), $parameter->getId());
		$productParameters = $this->productParameterRelatedRepo->findByProductId($source->getId());
		foreach ($productParameters as $productParameter) {
			if (in_array($productParameter->getParameterId(), $excludeParameterId) === FALSE) {
				$_productParameter = $this->productParameterFacade->add($target->getId(), $productParameter->getParameterId());
				unset($_productParameter);
			}
		}
		return $target;
	}



	/**
	 * @param $source Product
	 * @param $target Product
	 * @param $group ProductParameterGroupEntity
	 * @return Product
	 * @throws VariantStorageFacadeException
	 */
	private function saveVariant(Product $source,
								 Product $target,
								 ProductParameterGroupEntity $group) : Product
	{
		$variant = $this->productVariantFacade->add($source->getId(), $target->getId(), $group->getId());
		unset($variant);

		return $target;
	}



	/**
	 * @param $source Product
	 * @param $target Product
	 * @throws CopyFacadeException
	 */
	private function savePhotos(Product $source, Product $target)
	{
		try {
			//main photo
			if ($source->getPhoto()) {
				$name = $this->productPhotoManager->copy($source, $target);
				$target->setPhoto($name);
				$this->productRepo->save($target);
			}

			//additional photos
			$additionalPhotos = $this->productAdditionalPhotoRepo->findByProductId($source->getId());
			foreach ($additionalPhotos as $additionalPhoto) {
				$name = $this->productPhotoManager->copy($additionalPhoto, $target);
				$photo = $this->productAdditionalPhotoFactory->create($target, $name);
				$this->productAdditionalPhotoRepo->save($photo);
				unset($additionalPhoto, $photo);
			}
		} catch (PhotoManagerException $exception) {
			throw new CopyFacadeException($exception->getMessage());
		}
	}
}
