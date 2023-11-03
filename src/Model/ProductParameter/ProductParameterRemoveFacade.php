<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterRepository;
use App\Helpers\Entities;
use App\ProductParameterGroup\Lock\LockRepository;
use App\ProductParameterGroup\Lock\Parameter\ParameterRepository;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterRemoveFacade extends NObject
{


    /** @var CategoryFiltrationGroupParameterRepository */
    protected $categoryFiltrationGroupParameterRepo;

    /** @var CategoryFiltrationGroupRepository */
    protected $categoryFiltrationGroupRepo;

    /** @var LockRepository */
    protected $groupLockRepo;

    /** @var ParameterRepository */
    protected $groupLockParameterRepo;

    /** @var ProductParameterRepositoryFactory */
    protected $productParameterRepositoryFactory;

    /** @var callable[]|null */
    public $onRemove;



    public function __construct(CategoryFiltrationGroupParameterRepository $categoryFiltrationGroupParameterRepository,
                                CategoryFiltrationGroupRepository $categoryFiltrationGroupRepository,
                                LockRepository $lockRepo,
                                ParameterRepository $parameterRepo,
                                ProductParameterRepositoryFactory $productParameterRepositoryFactory)
    {
        $this->categoryFiltrationGroupParameterRepo = $categoryFiltrationGroupParameterRepository;
        $this->categoryFiltrationGroupRepo = $categoryFiltrationGroupRepository;
        $this->groupLockRepo = $lockRepo;
        $this->groupLockParameterRepo = $parameterRepo;
        $this->productParameterRepositoryFactory = $productParameterRepositoryFactory;
    }



    /**
     * @param int $id
     * @throws ProductParameterRemoveFacadeException
     */
    public function remove(int $id)
    {
        try {
            //repo
            $repo = $this->productParameterRepositoryFactory->create();

            //load
            $entity = $repo->getOneById($id);

            //check if parameter has a lock
            if ($locks = $this->groupLockRepo->findByGroupId($entity->getProductParameterGroupId())) {
                $lockId = Entities::getProperty($locks, 'id');
                $parameters = $this->groupLockParameterRepo->findByMoreLockIdAndParameterId($lockId, $entity->getId());
                if ($parameters) {
                    $keys = [];
                    foreach ($parameters as $parameter) {
                        $keys[] = $locks[$parameter->getLockId()]->getKey();
                    }
                    throw new ProductParameterRemoveFacadeException(sprintf('Parametr nelze smazat. Má návaznost na: %s', implode(', ', $keys)));
                }
            }

            //check if parameter is in a category filtration parameter group
            if ($groupParameters = $this->categoryFiltrationGroupParameterRepo->findByProductParameterId($entity->getId())) {
                $groupId = Entities::getProperty($groupParameters, 'categoryFiltrationGroupId');
                $groups = $this->categoryFiltrationGroupRepo->findByMoreId($groupId);
                $groupNames = Entities::getProperty($groups, 'name');
                throw new ProductParameterRemoveFacadeException(sprintf('Parameter nelze smazat. Je vložen v těchto skupinách filtrace kategorií: %s', implode(', ', $groupNames)));
            }

            $this->onRemove($entity);

            //remove
            $repo->remove($entity);
        } catch (ProductParameterNotFoundException $exception) {
            throw new ProductParameterRemoveFacadeException($exception->getMessage());
        }

    }
}