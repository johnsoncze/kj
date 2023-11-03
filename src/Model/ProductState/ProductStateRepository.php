<?php

declare(strict_types = 1);

namespace App\ProductState;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductStateRepository extends BaseRepository implements IRepositorySource, IRepository
{


    /** @var string */
    protected $entityName = ProductState::class;



    /**
     * @param int $id
     * @return ProductState
     * @throws ProductStateNotFoundException
     */
    public function getOneById(int $id) : ProductState
    {
        $result = $this->findOneBy([
            'where' => [
                ['id', '=', $id]
            ]
        ]);
        if (!$result) {
            throw new ProductStateNotFoundException(sprintf('Status produktů s id "%d" nebyl nalezen.', $id));
        }
        return $result;
    }



    /**
     * @return null|ProductState[]
     */
    public function findAllBySort()
    {
        return $this->findBy([
            'sort' => [
                'sort', 'ASC'
            ]
        ]);
    }



    /**
     * @param $id
     * @return ProductState[]|array
    */
    public function findByMoreId(array $id) : array
    {
        $filter['where'][] = ['id', '', $id];
        return $this->findBy($filter) ?: [];
    }
}