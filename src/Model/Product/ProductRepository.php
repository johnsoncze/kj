<?php

declare(strict_types = 1);

namespace App\Product;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use App\Product\Parameter\ProductParameter;
use Nette\Database\Table\IRow;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductRepository extends BaseRepository implements IRepositorySource, IRepository
{


    /** @var string */
    protected $entityName = Product::class;



    /**
     * @param int $id
     * @return Product
     * @throws ProductNotFoundException
     */
    public function getOneById(int $id) : Product
    {
        $result = $this->findOneBy([
            'where' => [
                ['id', '=', $id]
            ]
        ]);
        if (!$result) {
            throw new ProductNotFoundException(sprintf('Produkt s id "%d" nebyl nalezen.', $id));
        }
        return $result;
    }



    /**
     * Get one by external system id.
     * @param $id int
     * @return Product
     * @throws ProductNotFoundException
     */
    public function getOneByExternalSystemId(int $id) : Product
    {
        $product = $this->findOneByExternalSystemId($id);
        if (!$product) {
            throw new ProductNotFoundException(sprintf('Produkt s externím id \'%s\' nebyl nalezen.', $id));
        }
        return $product;
    }



    /**
     * @param $id int
     * @return Product|null
     */
    public function findOneByExternalSystemId(int $id)
    {
        $filters['where'][] = ['externalSystemId', '=', $id];
        return $this->findOneBy($filters) ?: NULL;
    }



    /**
     * @param $id array
     * @return array|Product[]
     */
    public function findByMoreId(array $id) : array
    {
        $filters['where'][] = ['id', '', $id];
        return $this->findBy($filters) ?: [];
    }



    /**
	 * @param $languageId int
	 * @param $type string
	 * @return array|IRow[]
    */
	public function findNotAsVariantListByLanguageIdAndType(int $languageId, string $type) : array
	{
		$filters['where'][] = 'pt_language_id = ' . $languageId;
		$filters['where'][] = ['type', '=', $type];
		$filters['where'][] = ['id', 'NOTIN.SQL', sprintf('(SELECT pv_product_variant_id FROM product_variant)')];
		$filters['join'][] = ['LEFT JOIN', 'product_translation', 'pt_product_id = p_id'];
		$filters['columns'] = ['p_id', 'p_code', 'pt_name'];

		return $this->getEntityMapper()
			->getQueryManager($this->getEntityName())
			->findBy($filters, function ($results) {
				$list = [];
				foreach ($results as $result) {
					list($id, $code, $name) = $result;
					$list[$id] = sprintf('%s - %s', $code, $name);
				}
				return $list;
			}) ?: [];
	}



    /**
     * @param $languageId int
     * @return array|IRow[]
     */
    public function findListByLanguageId(int $languageId) : array
    {
        $filters['where'][] = 'pt_language_id = ' . $languageId;
        $filters['join'][] = ['LEFT JOIN', 'product_translation', 'pt_product_id = p_id'];
        $filters['columns'] = ['p_id', 'p_code', 'pt_name'];

        $mapper = function ($results) {
            $list = [];
            foreach ($results as $result) {
                $list[$result['p_id']] = sprintf('%s - %s', $result['p_code'], $result['pt_name']);
            }
            return $list;
        };

        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy($filters, $mapper) ?: [];
    }



    /**
     * Find all external system id.
     * @return array
     */
    public function findExternalSystemId() : array
    {
        $mapper = function ($results) {
            $list = [];
            foreach ($results as $result) {
                $list[] = $result['p_external_system_id'];
            }
            return $list;
        };

        $filters['columns'] = ['externalSystemId'];

        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy($filters, $mapper) ?: [];
    }



    /**
     * @return array [externalSystemId => id,..]
     */
    public function findExternalSystemIdList() : array
    {
        $filters['where'][] = ['externalSystemId', 'NOT', NULL];
        $filters['columns'] = ['id', 'externalSystemId'];

        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy($filters, function ($result) {
                $list = [];
                foreach ($result as $value) {
                    $list[$value['p_external_system_id']] = $value['p_id'];
                }
                return $list;
            }) ?: [];
    }



    /**
     * @return CountDTO
     */
    public function countWithMissingProductFeedData() : CountDTO
    {
        $filter = $this->getMissingProductFeedDataCondition();
        return $this->count($filter);
    }



    /**
     * @param $limit int
     * @param $offset int
     * @return Product[]|array
     */
    public function findWithMissingProductFeedData(int $limit, int $offset) : array
    {
        $filter = $this->getMissingProductFeedDataCondition();
        $filter['limit'] = $limit;
        $filter['offset'] = $offset;
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $limit int
     * @param $offset int|null
     * @return Product[]|array
     */
    public function findByLimitAndOffset(int $limit, int $offset = NULL) : array
    {
        return $this->findBy([
            'limit' => $limit,
            'offset' => $offset
        ]) ?: [];
    }



    /**
	 * @param $code string
	 * @return Product|null
    */
	public function findOneByCode(string $code)
	{
		$filter['where'][] = ['code', '=', $code];
		return $this->findOneBy($filter) ?: NULL;
	}



	/**
	 * @return array
	*/
	public function findListWithoutExternalSystemId() : array
	{
		$filters['where'][] = ['externalSystemId', '', NULL];
		$filters['columns'] = ['id', 'code'];

		return $this->getEntityMapper()
			->getQueryManager($this->getEntityName())
			->findBy($filters, function ($result) {
				$list = [];
				foreach ($result as $value) {
					$list[$value['p_id']] = $value['p_code'];
				}
				return $list;
			}) ?: [];
	}



	/**
	 * @param $parameterId array
	 * @return array
	 */
	public function findProductIdByMoreParameterIdAsCategoryParameter(array $parameterId) : array
	{
		$productParameterRelation = ProductParameter::getAnnotation();
		$subQuery = sprintf('(SELECT %s FROM %s WHERE %s IN (\'%s\') GROUP BY %1$s HAVING COUNT(*) = \'%d\')',
			$productParameterRelation->getPropertyByName('productId')->getColumn()->getName(),
			$productParameterRelation->getTable()->getName(),
			$productParameterRelation->getPropertyByName('parameterId')->getColumn()->getName(),
			implode('\',\'', $parameterId),
			count($parameterId));

		$filter['columns'] = ['id'];
		$filter['where'][] = ['id', 'IN.SQL', $subQuery];

		$result = $this->getEntityMapper()
			->getQueryManager(Product::class)
			->findBy($filter, function ($rows) {
				$response = [];
				$productAnnotation = Product::getAnnotation();
				$columnId = $productAnnotation->getPropertyByName('id');
				foreach ($rows as $row) {
					$id = $row[$columnId->getColumn()->getName()];
					$response[$id] = $id;
				}
				return $response;
			});
		return $result ?: [];
	}



    /**
     * @return array
     */
    private function getMissingProductFeedDataCondition() : array
    {
        $filter['whereOr'][] = ['googleMerchantBrandText', '', NULL];
        $filter['whereOr'][] = ['googleMerchantCategory', '', NULL];
        $filter['whereOr'][] = ['zboziCzCategory', '', NULL];
        $filter['whereOr'][] = ['heurekaCategory', '', NULL];

        return $filter;
    }
}