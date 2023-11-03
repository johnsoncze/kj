<?php

declare(strict_types = 1);

namespace App\Product\Variant;

use App\Product\Parameter\ProductParameter;
use App\Product\Parameter\ProductParameterRepository;
use App\Product\Product;
use App\Product\ProductNotFoundException;
use App\Product\ProductRepository;
use App\ProductParameter\ProductParameterNotFoundException;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupNotFoundException;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use Nette\Application\LinkGenerator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class VariantStorageFacade
{


	/** @var LinkGenerator */
	private $linkGenerator;

	/** @var ProductRepository */
	private $productRepo;

	/** @var ProductParameterGroupRepository */
	private $parameterGroupRepo;

	/** @var ProductParameterRepository */
	private $productParameterRepo;

	/** @var VariantDuplication */
	private $variantDuplication;

	/** @var VariantRepository */
	private $variantRepo;



	public function __construct(ProductParameterRepository $productParameterRepository,
								LinkGenerator $linkGenerator,
								ProductParameterGroupRepository $productParameterGroupRepository,
								ProductRepository $productRepo,
								VariantDuplication $variantDuplication,
								VariantRepository $variantRepo)
	{
		$this->linkGenerator = $linkGenerator;
		$this->parameterGroupRepo = $productParameterGroupRepository;
		$this->productParameterRepo = $productParameterRepository;
		$this->productRepo = $productRepo;
		$this->variantDuplication = $variantDuplication;
		$this->variantRepo = $variantRepo;
	}



	/**
	 * Add a variant.
	 * @param $productId int
	 * @param $productVariantId int
	 * @param $groupId int
     * @param $parentVariantId int|null
	 * @return Variant
	 * @throws VariantStorageFacadeException
	 * todo test
	 */
	public function add(int $productId,
                        int $productVariantId,
                        int $groupId,
                        int $parentVariantId = NULL) : Variant
	{
		try {
			$product = $this->productRepo->getOneById($productId);
			$productVariant = $this->productRepo->getOneById($productVariantId);
			$group = $this->parameterGroupRepo->getOneById($groupId);
			$productVariantParameter = $this->productParameterRepo->findOneByProductIdAndGroupId($productVariant->getId(), $group->getId());
			$productParameter = $this->productParameterRepo->findOneByProductIdAndGroupId($product->getId(), $group->getId());

			if ($this->variantRepo->findOneByProductVariantId($productId)) {
				throw new VariantStorageFacadeException('Hlavní produkt je již variantou.');
			}

			if ($this->variantRepo->findOneByProductId($productVariantId)) {
				throw new VariantStorageFacadeException('Produkt, který se snažíte uložit jako variantu, je již hlavní produkt s variantami.');
			}

			if (!$productParameter) {
				$message = sprintf('Hlavní produkt nemá nastavený parametr z vybrané skupiny.');
				throw new VariantStorageFacadeException($message);
			}

			if (($v = $this->variantRepo->findOneByProductVariantId($productVariantId)) && (int)$v->getProductId() !== (int)$product->getId()) {
			    $p = $this->productRepo->getOneById($v->getProductId());
			    $message = sprintf('Produkt, který se snažíte uložit jako variantu, je již veden jako varianta k produktu \'%s\'.', $p->getCode());
			    throw new VariantStorageFacadeException($message);
            }

			if (!$productVariantParameter) {
				$link = $this->linkGenerator->link('Admin:Product:editParameter', ['id' => $productVariant->getId()]);
				$message = sprintf('Variantní produkt nemá nastavený parametr z vybrané skupiny. <a href="%s" target="_blank">Nastavte parametr zde</a>.', $link);
				throw new VariantStorageFacadeException($message);
			}

			if ($parentVariantId === NULL && (int)$productVariantParameter->getParameterId() === (int)$productParameter->getParameterId()) {
				throw new VariantStorageFacadeException('Variantní produkt má nastavený stejný parametr jako aktuální produkt.');
			}

			$variant = $this->createVariant($product, $productVariant, $productParameter, $productVariantParameter, $group);
			if ($parentVariantId !== NULL) {
			    $parentVariant = $this->getParentVariant($parentVariantId);
			    $variant->setParentVariantId($parentVariant->getId());
            }

			$this->variantDuplication->check($variant, $this->variantRepo);
			$this->variantRepo->save($variant);

			return $variant;
		} catch (ProductNotFoundException $exception) {
			throw new VariantStorageFacadeException($exception->getMessage());
		} catch (ProductParameterNotFoundException $exception) {
			throw new VariantStorageFacadeException($exception->getMessage());
		} catch (VariantDuplicationException $exception) {
			throw new VariantStorageFacadeException($exception->getMessage());
		} catch (ProductParameterGroupNotFoundException $exception) {
			throw new VariantStorageFacadeException($exception->getMessage());
		}
	}



	/**
	 * Remove variant.
	 * @param $id int
	 * @return Variant
	 * @throws VariantStorageFacadeException
	 */
	public function remove(int $id) : Variant
	{
		try {
			$variant = $this->variantRepo->getOneById($id);
			$this->variantRepo->remove($variant);
			return $variant;
		} catch (VariantNotFoundException $exception) {
			throw new VariantStorageFacadeException($exception->getMessage());
		}
	}



	/**
	 * Create variant object.
	 * @param $product Product
	 * @param $productVariant Product
	 * @param $productParameter ProductParameter
	 * @param $productVariantParameter ProductParameter
	 * @param $group ProductParameterGroupEntity
	 * @return Variant
	 */
	private function createVariant(Product $product,
								   Product $productVariant,
								   ProductParameter $productParameter,
								   ProductParameter $productVariantParameter,
								   ProductParameterGroupEntity $group) : Variant
	{
		$variant = new Variant();
		$variant->setProductId($product->getId());
		$variant->setProductParameterId($productParameter->getParameterId());
		$variant->setProductParameterRelationId($productParameter->getId());
		$variant->setProductVariantId($productVariant->getId());
		$variant->setProductVariantParameterId($productVariantParameter->getParameterId());
		$variant->setProductVariantParameterRelationId($productVariantParameter->getId());
		$variant->setParameterGroupId($group->getId());

		return $variant;
	}



	/**
     * @param $id int
     * @return Variant
     * @throws VariantStorageFacadeException
	*/
	private function getParentVariant(int $id) : Variant
    {
        try {
            return $this->variantRepo->getOneById($id);
        } catch (VariantNotFoundException $exception) {
            throw new VariantStorageFacadeException('Nadřazená varianta nebyla nalezena.');
        }
    }
}