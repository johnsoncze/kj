<?php

declare(strict_types = 1);

namespace App\Category;

use App\CategoryFiltration\CategoryFiltrationEntity;
use App\CategoryFiltration\CategoryFiltrationNotFoundException;
use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = CategoryFiltrationEntity::class;



    /**
     * @param int $id
     * @return CategoryFiltrationEntity
     * @throws CategoryFiltrationNotFoundException
     */
    public function getOneById(int $id) : CategoryFiltrationEntity
    {
        $result = $this->findOneBy([
            "where" => [
                ["id", "=", $id]
            ]
        ]);

        if (!$result) {
            throw new CategoryFiltrationNotFoundException(sprintf("Filtrace kategorie s id '%s' nebyla nalezena", $id));
        }

        return $result;
    }



    /**
     * @param int $catId
     * @param int $groupId
     * @return CategoryFiltrationEntity|null
     */
    public function findOneByCategoryIdAndProductParameterGroupId(int $catId, int $groupId)
    {
        return $this->findOneBy([
            "where" => [
                ["categoryId", "=", $catId],
                ["productParameterGroupId", "=", $groupId]
            ]
        ]);
    }



    /**
     * @param int $categoryId
     * @return CategoryFiltrationEntity[]|null
     */
    public function findByCategoryId(int $categoryId)
    {
        return $this->findBy([
            "where" => [
                ["categoryId", "=", $categoryId]
            ], "sort" => [
                ['LENGTH(sort)', 'sort'], "ASC"
            ]
        ]);
    }



    /**
     * Find joined.
     * @param $filters array
     * @return array
     */
    public function findJoined(array $filters) : array
    {
        $filters['join'] = $this->getParameterJoins();
        $mapping = function ($results) {
            return $results;
        };

        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy($filters, $mapping) ?: [];
    }



    /**
     * @param $filters array
     * @return CountDTO
     */
    public function countJoined(array $filters) : CountDTO
    {
        $filters['join'] = $this->getParameterJoins();
        return $this->count($filters);
    }



    /**
     * todo názvy nahradit daty z entity
     * @return array
     */
    public function getParameterJoins() : array
    {
        return [
            ['LEFT JOIN', 'product_parameter_group', 'ppg_id = cf_product_parameter_group_id'],
            ['LEFT JOIN', 'product_parameter_group_translation', 'ppgt_product_parameter_group_id = ppg_id'],
        ];
    }
}