<?php

declare(strict_types = 1);

namespace App\Product\Parameter;

use App\Extensions\Grido\IRepositorySource;
use App\NotFoundException;
use App\ProductParameter\ProductParameterEntity;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterRepository extends BaseRepository implements IRepositorySource
{


    /** @var string */
    protected $entityName = ProductParameter::class;



    /**
     * Get one by id.
     * @param $id int
     * @return ProductParameter
     * @throws NotFoundException
     */
    public function getOneById(int $id) : ProductParameter
    {
        $filters['where'][] = ['id', '=', $id];
        $result = $this->findOneBy($filters);
        if (!$result) {
            throw new NotFoundException('Parametr nebyl nalezen.');
        }
        return $result;
    }



    /**
     * @param int $productId
     * @return ProductParameter[]|null
     */
    public function findByProductId(int $productId)
    {
        return $this->findBy([
            'where' => [
                ['productId', '=', $productId]
            ]
        ]);
    }



    /**
     * @param $productId int
     * @param $parameterId int
     * @return ProductParameter|null
     */
    public function findOneByProductIdAndParameterId(int $productId, int $parameterId)
    {
        $filters['where'][] = ['productId', '=', $productId];
        $filters['where'][] = ['parameterId', '=', $parameterId];
        return $this->findOneBy($filters) ?: NULL;
    }



    /**
     * @param $productId array
     * @param $parameterId array
     * @return ProductParameter[]|array
     * todo rename to findByMoreProductIdAndMoreParameterId
     */
    public function findByProductIdAndParameterId(array $productId, array $parameterId) : array
    {
        $filters['where'][] = ['productId', '', $productId];
        $filters['where'][] = ['parameterId', '', $parameterId];
        return $this->findBy($filters) ?: [];
    }



    /**
     * @param $productId array
     * @param $parameterId array
     * @return array [parameterId => productCount,..]
     */
    public function getCountProductByMoreProductIdAndMoreParameterId(array $productId, array $parameterId) : array
    {
        $filters['group'] = ['parameterId'];
        $filters['columns'] = ['parameterId', 'COUNT(*) AS productCount'];
        $filters['where'][] = ['productId', '', $productId];
        $filters['where'][] = ['parameterId', '', $parameterId];

        $result = $this->getEntityMapper()
            ->getQueryManager(ProductParameter::class)
            ->findBy($filters, function ($rows) {
                $response = [];
                $productParameterAnnotation = ProductParameter::getAnnotation();
                $parameterIdColumn = $productParameterAnnotation->getPropertyByName('parameterId');
                foreach ($rows as $row) {
                    $response[$row[$parameterIdColumn->getColumn()->getName()]] = $row['productCount'];
                }
                return $response;
            });
        return $result ?: [];
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
     * @param $productId array
     * @param $groupId array
     * @return ProductParameter[]|array
     */
    public function findByMoreProductIdAndMoreGroupId(array $productId, array $groupId) : array
    {
        $productParameterAnnotation = ProductParameterEntity::getAnnotation();
        $subQuery = sprintf('(SELECT %s FROM %s WHERE %s IN (\'%s\') )',
            $productParameterAnnotation->getPropertyByName('id')->getColumn()->getName(),
            $productParameterAnnotation->getTable()->getName(),
            $productParameterAnnotation->getPropertyByName('productParameterGroupId')->getColumn()->getName(),
            implode('\',\'', $groupId));

        $filter['where'][] = ['productId', '', $productId];
        $filter['where'][] = ['parameterId', 'IN.SQL', $subQuery];
        return $this->findBy($filter) ?: [];
    }



	/**
	 * @param $productId array
	 * @return ProductParameter[]|array
	 */
	public function findByMoreProductId(array $productId) : array
	{
		$filter['where'][] = ['productId', '', $productId];
		return $this->findBy($filter) ?: [];
	}



    /**
     * @param $productId int
     * @param $groupId int
     * @return ProductParameter|NULL
     */
    public function findOneByProductIdAndGroupId(int $productId, int $groupId)
    {
        $productParameterAnnotation = ProductParameterEntity::getAnnotation();
        $subQuery = sprintf('(SELECT %s FROM %s WHERE %s IN (\'%s\') )',
            $productParameterAnnotation->getPropertyByName('id')->getColumn()->getName(),
            $productParameterAnnotation->getTable()->getName(),
            $productParameterAnnotation->getPropertyByName('productParameterGroupId')->getColumn()->getName(),
            $groupId);

        $filter['where'][] = ['productId', '=', $productId];
        $filter['where'][] = ['parameterId', 'IN.SQL', $subQuery];
        return $this->findOneBy($filter) ?: NULL;
    }



	/**
	 * @param $productId int
	 * @param $groupId int
	 * @return ProductParameter[]|array
	 */
    public function findByProductIdAndGroupId(int $productId, int $groupId) : array
	{
		$productParameterAnnotation = ProductParameterEntity::getAnnotation();
		$subQuery = sprintf('(SELECT %s FROM %s WHERE %s IN (\'%s\') )',
			$productParameterAnnotation->getPropertyByName('id')->getColumn()->getName(),
			$productParameterAnnotation->getTable()->getName(),
			$productParameterAnnotation->getPropertyByName('productParameterGroupId')->getColumn()->getName(),
			$groupId);

		$filter['where'][] = ['productId', '=', $productId];
		$filter['where'][] = ['parameterId', 'IN.SQL', $subQuery];
		return $this->findBy($filter) ?: [];
	}



    /**
     * todo názvy nahradit daty z entity
     * @return array
     */
    protected function getParameterJoins() : array
    {
        return [
            ['LEFT JOIN', 'product_parameter', 'pp_id = ppr_parameter_id'],
            ['LEFT JOIN', 'product_parameter_translation', 'ppt_product_parameter_id = pp_id'],
            ['LEFT JOIN', 'product_parameter_group', 'ppg_id = pp_product_parameter_group_id'],
            ['LEFT JOIN', 'product_parameter_group_translation', 'ppgt_product_parameter_group_id = ppg_id'],
        ];
    }
}