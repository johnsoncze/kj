<?php

namespace App;

use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Exceptions\RepositoryException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IRepository
{


    /**
     * Save entity
     * @param $entity IEntity|IEntity[]
     * @return IEntity|IEntity[]
     */
    public function save($entity);



    /**
     * Remove entity
     * @param $entity
     * @return int
     * @throws RepositoryException
     */
    public function remove($entity);



    /**
     * Find one by filters
     * @param $filters array
     * @return IEntity[]|null
     */
    public function findOneBy(array $filters);



    /**
     * Find by filters
     * @param $filters array
     * @return IEntity[]|null
     */
    public function findBy(array $filters);



    /**
     * Find all
     * @return IEntity[]|null
     */
    public function findAll();
}