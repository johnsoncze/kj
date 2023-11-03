<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Repositories\Traits;

use Ricaefeliz\Mappero\Repositories\RepositoryException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ReadOnlyTrait
{


    public function save($entity)
    {
        throw new RepositoryException($this->getErrorMessage());
    }



    public function remove($entity)
    {
        throw new RepositoryException($this->getErrorMessage());
    }



    public function delete($entity)
    {
        throw new RepositoryException($this->getErrorMessage());
    }



    /**
     * @return string
     */
    private function getErrorMessage() : string
    {
        return 'This repository is only for read.';
    }
}