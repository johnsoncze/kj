<?php

declare(strict_types = 1);

namespace App\Product\Parameter;

use App\NotFoundException;
use App\Product\Product;
use App\Product\ProductNotFoundException;
use App\Product\ProductRepository;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterNotFoundException;
use App\ProductParameter\ProductParameterRepositoryFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ParameterStorageFacade
{


    /** @var ParameterDuplication */
    private $parameterDuplication;

    /** @var \App\Product\Parameter\ProductParameterRepositoryFactory */
    private $productParameterRepoFactory;

    /** @var ProductRepository */
    private $productRepo;

    /** @var ProductParameterRepositoryFactory */
    private $parameterRepoFactory;



    public function __construct(ParameterDuplication $parameterDuplication,
                                \App\Product\Parameter\ProductParameterRepositoryFactory $productParameterRepoFactory,
                                ProductRepository $productRepo,
                                ProductParameterRepositoryFactory $parameterRepoFactory)
    {
        $this->parameterDuplication = $parameterDuplication;
        $this->productParameterRepoFactory = $productParameterRepoFactory;
        $this->productRepo = $productRepo;
        $this->parameterRepoFactory = $parameterRepoFactory;
    }



    /**
     * Add a new parameter.
     * @param $productId int
     * @param $parameterId int
     * @return ProductParameter
     * @throws ParameterStorageException
     */
    public function add(int $productId, int $parameterId) : ProductParameter
    {
        $productParameterRepo = $this->productParameterRepoFactory->create();
        $parameterRepo = $this->parameterRepoFactory->create();

        try {
            $product = $this->productRepo->getOneById($productId);
            $parameter = $parameterRepo->getOneById($parameterId);
            $productParameter = $this->createProductParameter($product, $parameter);
            $this->parameterDuplication->check($productParameter, $productParameterRepo);
            return $productParameterRepo->save($productParameter);
        } catch (ProductNotFoundException $exception) {
            throw new ParameterStorageException($exception->getMessage());
        } catch (ParameterDuplicationException $exception) {
            throw new ParameterStorageException($exception->getMessage());
        } catch (ProductParameterNotFoundException $exception) {
            throw new ParameterStorageException($exception->getMessage());
        }
    }



    /**
     * Remove parameter.
     * @param $parameterId int
     * @return bool
     * @throws ParameterStorageException
     */
    public function remove(int $parameterId) : bool
    {
        try {
            $productParameterRepo = $this->productParameterRepoFactory->create();
            $productParameter = $productParameterRepo->getOneById($parameterId);
            $productParameterRepo->remove($productParameter);
            return TRUE;
        } catch (NotFoundException $exception) {
            throw new ParameterStorageException('Parametr nebyl nalezen.');
        }
    }



    /**
     * Create product parameter.
     * @param $product Product
     * @param $productParameter ProductParameterEntity
     * @return ProductParameter
     */
    private function createProductParameter(Product $product, ProductParameterEntity $productParameter) : ProductParameter
    {
        $parameter = new ProductParameter();
        $parameter->setProductId($product->getId());
        $parameter->setParameterId($productParameter->getId());
        $parameter->setAddDate(new \DateTime());

        return $parameter;
    }
}