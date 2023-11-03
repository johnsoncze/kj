<?php

declare(strict_types = 1);

namespace App\Category\Product\Related;

use App\Extensions\Grido\IRepositorySource;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductRepository extends BaseRepository implements IRepositorySource
{


    /** @var string */
    protected $entityName = Product::class;



    /**
	 * @param $id int[]
	 * @return Product[]|array
    */
	public function findByMoreId(array $id) : array
	{
		$filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
		$filter['where'][] = ['id', '', $id];
		return $this->findBy($filter) ?: [];
	}



    /**
     * @param $categoryId int
     * @param $productId int
	 * @param $type string
     * @return Product|null
     */
    public function findOneByCategoryIdAndProductIdAndType(int $categoryId, int $productId, string $type)
    {
        $filter['where'][] = ['categoryId', '=', $categoryId];
        $filter['where'][] = ['productId', '=', $productId];
        $filter['where'][] = ['type', '=', $type];
        return $this->findOneBy($filter) ?: NULL;
    }



    /**
     * @param $categoryId array
     * @return Product[]|array
     */
    public function findByMoreCategoryId(array $categoryId) : array
    {
        $filter['where'][] = ['categoryId', '', $categoryId];
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    /**
	 * @param $categoryId array
	 * @param $type string
	 * @return Product[]|array
    */
	public function findPublishedByMoreCategoryIdAndType(array $categoryId, string $type) : array
	{
		//todo replace query by property from product
		$subQuery = sprintf('(SELECT p_id FROM product WHERE p_state = \'%s\')', \App\Product\Product::PUBLISH);
		$filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
		$filter['where'][] = ['categoryId', '', $categoryId];
		$filter['where'][] = ['productId', 'IN.SQL', $subQuery];
		$filter['where'][] = ['type', '=', $type];
		return $this->findBy($filter) ?: [];
	}



    /**
	 * @param $categoryId int
	 * @param $limit int
	 * @param $offset int
	 * @return Product[]|array
    */
    public function findPublishedByCategoryIdAndLimitAndOffset(int $categoryId, int $limit, int $offset) : array
	{
		//todo replace query by property from product
		$subQuery = sprintf('(SELECT p_id FROM product WHERE p_state = \'%s\')', \App\Product\Product::PUBLISH);
		$filter['limit'] = $limit;
		$filter['offset'] = $offset;
		$filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
		$filter['where'][] = ['categoryId', '=', $categoryId];
		$filter['where'][] = ['productId', 'IN.SQL', $subQuery];
		return $this->findBy($filter) ?: [];
	}


    /**
	 * @param $categoryId int
	 * @return Product[]|array
    */
    public function findHomepageProductsByCategoryId(int $categoryId) : array
	{
		//todo replace query by property from product
        $subQuery = sprintf('(SELECT p_id FROM product WHERE p_state = \'%s\')', \App\Product\Product::PUBLISH);
		$filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
		$filter['where'][] = ['categoryId', '=', $categoryId];
		$filter['where'][] = ['type', '=', 'image_template'];
        $filter['where'][] = ['productId', 'IN.SQL', $subQuery];
		return $this->findBy($filter) ?: [];
	}	

	
    /**
     * @param $filter array
     * @return array
     */
    public function findJoined(array $filter) : array
    {
        $filter['join'] = $this->getRelatedJoins();
        $mapping = function ($results) {
            return $results;
        };

        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy($filter, $mapping) ?: [];
    }



    /**
	 * @param $categoryId int
	 * @param $type string
	 * @param $languageId int
	 * @return array
    */
    public function findJoinedByCategoryIdAndTypeAndLanguageId(int $categoryId, string $type, int $languageId) : array
	{
		$filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
		$filter['where'][] = ['type', '=', $type];
		$filter['where'][] = ['categoryId', '=', $categoryId];
		$filter['where'][] = ['pt_language_id', '=', $languageId];
		return $this->findJoined($filter);
	}

	

    /**
     * @param $filters array
     * @return CountDTO
     */
    public function countJoined(array $filters) : CountDTO
    {
        $filters['join'] = $this->getRelatedJoins();
        return $this->count($filters);
    }



    /**
     * @return array
     * @todo replace names by data from entity annotations
     */
    protected function getRelatedJoins() : array
    {
        return [
            ['LEFT JOIN', 'product', 'p_id = clp_product_id'],
            ['LEFT JOIN', 'product_translation', 'pt_product_id = p_id'],
        ];
    }
}