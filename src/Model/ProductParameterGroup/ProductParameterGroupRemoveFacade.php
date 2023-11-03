<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup;

use App\Helpers\Entities;
use App\ProductParameterGroup\Lock\LockRepository;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupRemoveFacade extends NObject
{


    /** @var LockRepository */
    protected $groupLockRepo;

    /**
     * @var ProductParameterGroupRepositoryFactory
     */
    protected $productParameterGroupRepositoryFactory;

    /** @var callable[]|null */
    public $onRemove;



    public function __construct(LockRepository $lockRepository,
                                ProductParameterGroupRepositoryFactory $productParameterGroupRepositoryFactory)
    {
        $this->groupLockRepo = $lockRepository;
        $this->productParameterGroupRepositoryFactory = $productParameterGroupRepositoryFactory;
    }



    /**
     * @param int $id
     * @return bool
     * @throws ProductParameterGroupRemoveFacadeException
     */
    public function remove(int $id) : bool
    {
        try {
            //Repo
            $repo = $this->productParameterGroupRepositoryFactory->create();
            $group = $repo->getOneById($id);

            if ($locks = $this->groupLockRepo->findByGroupId($id)) {
                $keys = Entities::getProperty($locks, 'key');
                $message = sprintf('Skupinu parametrů nelze smazat. Má návaznosti na: %s', implode(', ', $keys));
                throw new ProductParameterGroupRemoveFacadeException($message);
            }

            $this->onRemove($group);

            //Remove
            $repo->remove($group);

            return TRUE;
        } catch (ProductParameterGroupNotFoundException $exception) {
            throw new ProductParameterGroupRemoveFacadeException($exception->getMessage());
        }
    }

}