<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = ProductParameterGroupEntity::class;



    /**
     * @param int $id
     * @return ProductParameterGroupEntity
     * @throws ProductParameterGroupNotFoundException
     */
    public function getOneById(int $id)
    {
        $result = $this->findOneBy([
            "where" => [
                ["id", "=", $id]
            ]
        ]);
        if (!$result) {
            throw new ProductParameterGroupNotFoundException(sprintf("Skupina parametrů s id '%s' nebyla nalezena.", $id));
        }
        return $result;
    }



    /**
     * @param int $languageId
     * @return ProductParameterGroupEntity[]|null
     */
    public function findByLanguageId(int $languageId)
    {
        return $this->findBy([
            "where" => [
                ["languageId", "=", $languageId]
            ]
        ]);
    }



    /**
     * @return ProductParameterGroupEntity[]|array
     */
    public function findVisibleInOrder() : array
    {
        $filter['where'][] = ['visibleInOrder', '=', TRUE];
		$filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $id
     * @return ProductParameterGroupEntity[]|array
     */
    public function findByMoreId(array $id) : array
    {
        $filter['where'][] = ['id', '', $id];
		$filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }
}