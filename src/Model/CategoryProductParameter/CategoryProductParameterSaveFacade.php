<?php

declare(strict_types = 1);

namespace App\CategoryProductParameter;

use App\Category\CategoryEntity;
use App\Category\CategoryNotFoundException;
use App\Category\CategoryRepository;
use App\Helpers\Entities;
use App\NotFoundException;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterNotFoundException;
use App\ProductParameter\ProductParameterRepositoryFactory;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryProductParameterSaveFacade extends NObject
{


    /**
     * @var CategoryProductParameterRepositoryFactory
     */
    protected $categoryProductParameterRepositoryFactory;

    /** @var CategoryRepository */
    protected $categoryRepo;

    /** @var ProductParameterRepositoryFactory */
    protected $productParameterRepositoryFactory;



    public function __construct(CategoryProductParameterRepositoryFactory $categoryProductParameterRepositoryFactory,
                                CategoryRepository $categoryRepository,
                                ProductParameterRepositoryFactory $productParameterRepositoryFactory)
    {
        $this->categoryProductParameterRepositoryFactory = $categoryProductParameterRepositoryFactory;
        $this->categoryRepo = $categoryRepository;
        $this->productParameterRepositoryFactory = $productParameterRepositoryFactory;
    }



    /**
     * Add parameter to category.
     * @param $categoryId int
     * @param $parameterId int
     * @return CategoryProductParameterEntity
     * @throws CategoryProductParameterSaveFacadeException
     * todo test
     */
    public function add(int $categoryId, int $parameterId) : CategoryProductParameterEntity
    {
        $parameterRepo = $this->productParameterRepositoryFactory->create();
        $categoryParameterRepo = $this->categoryProductParameterRepositoryFactory->create();

        try {
            $category = $this->categoryRepo->getOneById($categoryId);
            $parameter = $parameterRepo->getOneById($parameterId);
            $categoryParameter = $categoryParameterRepo->findOneByCategoryIdAndParameterId($category->getId(), $parameter->getId());
            if ($categoryParameter) {
                throw new CategoryProductParameterSaveFacadeException('Parametr je již přiřazen.');
            }
            $categoryParameter = $this->createParameter($category, $parameter);
            $categoryParameterRepo->save($categoryParameter);
            return $categoryParameter;
        } catch (CategoryNotFoundException $exception) {
            throw new CategoryProductParameterSaveFacadeException($exception->getMessage());
        } catch (ProductParameterNotFoundException $exception) {
            throw new CategoryProductParameterSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $id int
     * @return bool
     * @throws CategoryProductParameterSaveFacadeException
    */
    public function remove(int $id) : bool
    {
        $categoryParameterRepo = $this->categoryProductParameterRepositoryFactory->create();

        try{
            $parameter = $categoryParameterRepo->getByMoreId([$id]);
            $parameter = end($parameter);
            $categoryParameterRepo->remove($parameter);
            return TRUE;
        } catch (NotFoundException $exception){
            throw new CategoryProductParameterSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * @param CategoryEntity $categoryEntity
     * @param int[] $parametersId
     * todo smazat
     */
    public function save(CategoryEntity $categoryEntity, array $parametersId = [])
    {
        //load actual parameters
        $repo = $this->categoryProductParameterRepositoryFactory->create();
        $parameters = $repo->findByCategoryId($categoryEntity->getId());

        //compare
        $result = Entities::searchValues($parameters, $parametersId, "productParameterId");

        //new..
        if (isset($result[Entities::VALUE_NOT_FOUND])) {
            $forSave = $this->createNew($categoryEntity, $result[Entities::VALUE_NOT_FOUND]);
            $repo->save($forSave);
        }

        //for remove..
        if (isset($result[Entities::ENTITY_WITHOUT_VALUE])) {
            $repo->remove($result[Entities::ENTITY_WITHOUT_VALUE]);
        }
    }



    /**
     * @param CategoryEntity $categoryEntity
     * @param array $parametersId
     * @return array
     * @throws CategoryProductParameterSaveFacadeException
     */
    protected function createNew(CategoryEntity $categoryEntity, array $parametersId) : array
    {
        //load parameters
        $parameterRepo = $this->productParameterRepositoryFactory->create();
        $parameters = $parameterRepo->findById($parametersId);

        $entities = [];
        foreach ($parametersId as $id) {
            if (!isset($parameters[$id])) {
                throw new CategoryProductParameterSaveFacadeException(sprintf("Parameter s id '%s' není dostupný.", $id));
            }
            $entity = $this->createParameter($categoryEntity, $parameters[$id]);
            $entities[] = $entity;
        }

        return $entities;
    }



    /**
     * Create parameter object.
     * @param $category CategoryEntity
     * @param $productParameter ProductParameterEntity
     * @return CategoryProductParameterEntity
     */
    protected function createParameter(CategoryEntity $category, ProductParameterEntity $productParameter) : CategoryProductParameterEntity
    {
        $parameter = new CategoryProductParameterEntity();
        $parameter->setCategoryId($category->getId());
        $parameter->setProductParameterId($productParameter->getId());
        return $parameter;
    }
}