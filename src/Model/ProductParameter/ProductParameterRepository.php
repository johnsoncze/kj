<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = ProductParameterEntity::class;



    /**
     * Get by more id.
     * @param $id array
     * @return array
     * @throws ProductParameterNotFoundException
     */
    public function getByMoreId(array $id) : array
    {
        $filters['where'][] = ['id', '', $id];
        $filters['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        $result = $this->findBy($filters);
        foreach ($id as $k => $_id) {
            if (isset($result[$_id])) {
                unset($id[$k]);
            }
        }
        if ($id) {
            throw new ProductParameterNotFoundException(sprintf('Položky s id \'%s\' nebyly nalezeny.', implode(',', $id)));
        }
        return $result;
    }



    /**
     * @param array $id
     * @return ProductParameterEntity[]|null
     */
    public function findById(array $id)
    {
        return $this->findBy([
            "where" => [
                ["id", "", $id],
            ]
        ]);
    }



    /**
     * @param int $groupId
     * @return ProductParameterEntity[]|null
     */
    public function findByProductParameterGroupId(int $groupId)
    {
        return $this->findBy([
            "where" => [
                ["productParameterGroupId", "=", $groupId]
            ], "sort" => [
                ['LENGTH(sort)', 'sort'],
                "ASC"
            ]
        ]);
    }



    /**
     * @param array $groupId
     * @return ProductParameterEntity[]|null
     */
    public function findByProductParameterGroupsId(array $groupId)
    {
        return $this->findBy([
            "where" => [
                ["productParameterGroupId", "", $groupId]
            ]
        ]);
    }



    /**
     * @param int $id
     * @return ProductParameterEntity
     * @throws ProductParameterNotFoundException
     */
    public function getOneById(int $id)
    {
        $result = $this->findOneBy([
            "where" => [
                ["id", "=", $id]
            ]
        ]);

        if (!$result) {
            throw new ProductParameterNotFoundException(sprintf("Parameter s id '%s' nebyl nalezen.", $id));
        }

        return $result;
    }



    /**
     * @param $id array
     * @return ProductParameterEntity[]|array
     */
    public function findByMoreId(array $id) : array
    {
        $filter['where'][] = ['id', '', $id];
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $groupId array
     * @return array
     */
    public function findByMoreGroupId(array $groupId) : array
    {
        $filter['where'][] = ['productParameterGroupId', '', $groupId];
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }
}