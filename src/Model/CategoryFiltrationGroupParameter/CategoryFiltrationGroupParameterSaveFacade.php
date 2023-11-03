<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroupParameter;

use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupParameterCheckDuplicate;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepositoryFactory;
use App\Helpers\Entities;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterRepositoryFactory;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationGroupParameterSaveFacade extends NObject
{


    /** @var CategoryFiltrationGroupParameterRepositoryFactory */
    protected $categoryFiltrationGroupParameterRepositoryFactory;

    /** @var CategoryFiltrationGroupRepositoryFactory */
    protected $categoryFiltrationGroupRepositoryFactory;

    /** @var ProductParameterRepositoryFactory */
    protected $productParameterRepositoryFactory;



    public function __construct(CategoryFiltrationGroupParameterRepositoryFactory $categoryFiltrationGroupParameterRepositoryFactory,
                                CategoryFiltrationGroupRepositoryFactory $categoryFiltrationGroupRepositoryFactory,
                                ProductParameterRepositoryFactory $productParameterRepositoryFactory)
    {
        $this->categoryFiltrationGroupParameterRepositoryFactory = $categoryFiltrationGroupParameterRepositoryFactory;
        $this->categoryFiltrationGroupRepositoryFactory = $categoryFiltrationGroupRepositoryFactory;
        $this->productParameterRepositoryFactory = $productParameterRepositoryFactory;
    }



    /**
     * @param CategoryFiltrationGroupEntity $groupEntity
     * @param array $productParametersId
     * @return CategoryFiltrationGroupParameterEntity[]
     * @throws CategoryFiltrationGroupParameterSaveFacadeException
     */
    public function save(CategoryFiltrationGroupEntity $groupEntity, array $productParametersId) : array
    {
        try {
            $groupRepo = $this->categoryFiltrationGroupRepositoryFactory->create();
            $parameterRepo = $this->categoryFiltrationGroupParameterRepositoryFactory->create();

            $this->checkParameters($productParametersId);
            $this->saveProcess($parameterRepo, $groupEntity, $productParametersId);

            $parametersFromStorage = $parameterRepo->findByCategoryFiltrationGroupId($groupEntity->getId());

            $this->checkDuplicate($groupEntity, $groupRepo, $parameterRepo, $parametersFromStorage);

            return $parametersFromStorage;
        } catch (CategoryFiltrationGroupParameterCheckDuplicateException $exception) {
            throw new CategoryFiltrationGroupParameterSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * Check if exists parameters
     *
     * @param array $productParametersId
     * @return ProductParameterEntity[]
     * @throws CategoryFiltrationGroupParameterSaveFacadeException
     */
    protected function checkParameters(array $productParametersId) : array
    {
        $productParameterRepo = $this->productParameterRepositoryFactory->create();
        $productParameters = $productParameterRepo->findById($productParametersId);
        $productParametersIdFromStorage = $productParameters ? Entities::getProperty($productParameters, 'id') : [];

        if (array_diff($productParametersIdFromStorage, $productParametersId) || array_diff($productParametersId, $productParametersIdFromStorage)) {
            throw new CategoryFiltrationGroupParameterSaveFacadeException('Některý parametr již neexistuje. Není možné uložit kombinaci parametrů.');
        }

        return $productParameters;
    }



    /**
     * Save process
     *
     * @param CategoryFiltrationGroupParameterRepository $parameterRepo
     * @param CategoryFiltrationGroupEntity $groupEntity
     * @param array $productParametersId
     */
    protected function saveProcess(CategoryFiltrationGroupParameterRepository $parameterRepo,
                                   CategoryFiltrationGroupEntity $groupEntity,
                                   array $productParametersId)
    {
        //load all parameters of actual group
        $groupParameters = $parameterRepo->findByCategoryFiltrationGroupId($groupEntity->getId());

        //compare
        $result = Entities::searchValues($groupParameters, $productParametersId, "productParameterId");

        //save a new
        $forSave = [];
        $notFound = isset($result[Entities::VALUE_NOT_FOUND]) ? $result[Entities::VALUE_NOT_FOUND] : [];
        $entityFactory = new CategoryFiltrationGroupParameterEntityFactory();
        foreach ($notFound as $parameterId) {
            $groupParameter = $entityFactory->create($groupEntity->getId(), $parameterId);
            $forSave[] = $groupParameter;
        }
        if ($forSave) {
            $parameterRepo->save($forSave);
        }

        //remove unused
        $forRemove = isset($result[Entities::ENTITY_WITHOUT_VALUE]) ? $result[Entities::ENTITY_WITHOUT_VALUE] : [];
        if ($forRemove) {
            $parameterRepo->remove($forRemove);
        }
    }



    /**
     * Check if combination of parameters exists already in another group of same category
     *
     * @param CategoryFiltrationGroupEntity $groupEntity
     * @param CategoryFiltrationGroupRepository $groupRepo
     * @param CategoryFiltrationGroupParameterRepository $parameterRepo
     * @param $productParameters CategoryFiltrationGroupParameterEntity[]
     * @return CategoryFiltrationGroupEntity
     */
    protected function checkDuplicate(CategoryFiltrationGroupEntity $groupEntity,
                                      CategoryFiltrationGroupRepository $groupRepo,
                                      CategoryFiltrationGroupParameterRepository $parameterRepo,
                                      array $productParameters)
    : CategoryFiltrationGroupEntity
    {
        //load all group of category
        $groups = $groupRepo->findByCategoryWithoutCategoryFiltrationGroupId(
            $groupEntity->getCategoryId(),
            $groupEntity->getId());

        //check duplicate
        if ($groups) {
            $groupsId = Entities::getProperty($groups, 'id');
            $parameters = $parameterRepo->findByCategoryFiltrationGroupsIdAndProductParametersId($groupsId, Entities::getProperty($productParameters, 'productParameterId'));

            if ($parameters) {
                $checked = new CategoryFiltrationGroupParameterCheckDuplicate();

                //load parameters from the first group for check
                foreach ($parameters as $parameter) {
                    $checked->check($productParameters, $parameterRepo->findByCategoryFiltrationGroupId($parameter->getCategoryFiltrationGroupId()));
                }
            }
        }

        return $groupEntity;
    }
}