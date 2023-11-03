<?php

declare(strict_types = 1);

namespace App\Product\Related;

use App\Extensions\Grido\IRepositorySource;
use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class RelatedRepository extends BaseRepository implements IRepositorySource
{


    /** @var string */
    protected $entityName = Related::class;



    /**
     * Get one by id.
     * @param $id int
     * @return Related
     * @throws NotFoundException
     */
    public function getOneById(int $id) : Related
    {
        $filters['where'][] = ['id', '=', $id];
        $result = $this->findOneBy($filters);
        if (!$result) {
            throw new NotFoundException('Produkt nebyl nalezen.');
        }
        return $result;
    }



    /**
     * @param $productId int
     * @param $productRelatedId int
     * @param $type string|int
     * @return Related|null
     */
    public function findOneByProductIdAndProductRelatedIdAndType(int $productId, int $productRelatedId, $type)
    {
        $filters['where'][] = ['productId', '=', $productId];
        $filters['where'][] = ['relatedProductId', '=', $productRelatedId];
        $filters['where'][] = ['type', '=', $type];
        return $this->findOneBy($filters) ?: NULL;
    }



    /**
     * Find joined.
     * @param $filters array
     * @return array
     */
    public function findJoined(array $filters) : array
    {
        $filters['join'] = $this->getRelatedJoins();
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
    public function countJoined(array $filters)
    {
        $filters['join'] = $this->getRelatedJoins();
        return $this->count($filters);
    }



    /**
     * @param $productId int
     * @param $type string
     * @return Related[]|array
     */
    public function findByProductIdAndType(int $productId, string $type) : array
    {
        $filter['where'][] = ['productId', '=', $productId];
        $filter['where'][] = ['type', '=' , $type];
        return $this->findBy($filter) ?: [];
    }



    /**
	 * @param $productId int
	 * @return Related[]|array
    */
	public function findByProductId(int $productId) : array
	{
		$filter['where'][] = ['productId', '=', $productId];
		return $this->findBy($filter) ?: [];
	}



    /**
     * @param $productId array
     * @param $type string|int
     * @return Related[]
    */
    public function findByMoreProductIdAndType(array $productId, $type) : array
    {
        $filter['where'][] = ['productId', '', $productId];
        $filter['where'][] = ['type', '=' , $type];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @return array
     * @todo replace names by data from entity annotations
     */
    protected function getRelatedJoins() : array
    {
        return [
            ['LEFT JOIN', 'product', 'p_id = pr_related_product_id'],
            ['LEFT JOIN', 'product_translation', 'pt_product_id = p_id'],
        ];
    }
}