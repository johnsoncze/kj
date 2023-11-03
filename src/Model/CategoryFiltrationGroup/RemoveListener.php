<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroup;

use App\CategoryFiltration\CategoryFiltrationEntity;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterRepositoryFactory;
use App\Helpers\Entities;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterRepositoryFactory;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use Kdyby\Events\Subscriber;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class RemoveListener extends NObject implements Subscriber
{


    /** @var CategoryFiltrationGroupRepositoryFactory */
    protected $categoryFiltrationGroupRepositoryFactory;

    /** @var CategoryFiltrationGroupParameterRepositoryFactory */
    protected $categoryFiltrationGroupParameterRepositoryFactory;

    /** @var ProductParameterRepositoryFactory */
    protected $productParameterRepositoryFactory;



    public function __construct(CategoryFiltrationGroupRepositoryFactory $categoryFiltrationGroupRepositoryFactory,
                                CategoryFiltrationGroupParameterRepositoryFactory $categoryFiltrationGroupParameterRepositoryFactory,
                                ProductParameterRepositoryFactory $productParameterRepositoryFactory)
    {
        $this->categoryFiltrationGroupRepositoryFactory = $categoryFiltrationGroupRepositoryFactory;
        $this->categoryFiltrationGroupParameterRepositoryFactory = $categoryFiltrationGroupParameterRepositoryFactory;
        $this->productParameterRepositoryFactory = $productParameterRepositoryFactory;
    }



    /**
     * @return array
     */
    public function getSubscribedEvents() : array
    {
        return [
            'App\CategoryFiltration\CategoryFiltrationRemoveFacade::onRemove' => 'onRemoveCategoryFiltration',
            'App\ProductParameterGroup\ProductParameterGroupRemoveFacade::onRemove' => 'onRemoveParameterGroup',
            'App\ProductParameter\ProductParameterRemoveFacade::onRemove' => 'onRemoveParameter',
        ];
    }



    /**
     * @param CategoryFiltrationEntity $filtrationEntity
     * @return CategoryFiltrationEntity
     */
    public function onRemoveCategoryFiltration(CategoryFiltrationEntity $filtrationEntity)
    : CategoryFiltrationEntity
    {
        //load all parameters of group that is in relation with category filtration
        $parameterRepo = $this->productParameterRepositoryFactory->create();
        $parameters = $parameterRepo->findByProductParameterGroupId((int)$filtrationEntity->getProductParameterGroupId());

        if ($parameters) {
            $parametersId = Entities::getProperty($parameters, 'id');

            //load these parameters from category filtration groups
            $groupParameterRepo = $this->categoryFiltrationGroupParameterRepositoryFactory->create();
            $groupParameters = $groupParameterRepo->findByProductParametersId($parametersId);

            if ($groupParameters) {
                $groupsId = Entities::getProperty($groupParameters, 'categoryFiltrationGroupId');

                //remove
                $groupRepo = $this->categoryFiltrationGroupRepositoryFactory->create();
                $groups = $groupRepo->findByGroupsIdAndCategoryId($groupsId, (int)$filtrationEntity->getCategoryId());

                if($groups){
                    $groupRepo->remove($groups);
                }
            }
        }

        return $filtrationEntity;
    }



    /**
     * @param ProductParameterGroupEntity $groupEntity
     * @return ProductParameterGroupEntity
     */
    public function onRemoveParameterGroup(ProductParameterGroupEntity $groupEntity)
    : ProductParameterGroupEntity
    {
        //load all parameters from group which will be remove
        $parameterRepo = $this->productParameterRepositoryFactory->create();
        $parameters = $parameterRepo->findByProductParameterGroupId($groupEntity->getId());

        if ($parameters) {
            $parametersId = Entities::getProperty($parameters, 'id');

            //load all category filtration groups which have some parameter which will be remove
            $groupParameterRepo = $this->categoryFiltrationGroupParameterRepositoryFactory->create();
            $groupParameters = $groupParameterRepo->findByProductParametersId($parametersId);

            if ($groupParameters) {
                $this->removeGroups(Entities::getProperty($groupParameters, 'categoryFiltrationGroupId'));
            }
        }

        return $groupEntity;
    }



    /**
     * @param ProductParameterEntity $parameter
     * @return ProductParameterEntity
     */
    public function onRemoveParameter(ProductParameterEntity $parameter)
    : ProductParameterEntity
    {
        //load all category filtration groups which have parameter which will be remove
        $groupParameterRepo = $this->categoryFiltrationGroupParameterRepositoryFactory->create();
        $groupParameters = $groupParameterRepo->findByProductParameterId((int)$parameter->getId());

        if ($groupParameters) {
            $this->removeGroups(Entities::getProperty($groupParameters, 'categoryFiltrationGroupId'));
        }

        return $parameter;
    }



    /**
     * @param $groupsId int[]
     * @return bool
     */
    protected function removeGroups(array $groupsId) : bool
    {
        //remove category filtration parameters
        $groupRepo = $this->categoryFiltrationGroupRepositoryFactory->create();
        $groups = $groupRepo->findById($groupsId);

        if($groups){
            $groupRepo->remove($groups);
        }

        return TRUE;
    }
}