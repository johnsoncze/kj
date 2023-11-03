<?php

declare(strict_types = 1);

namespace App\Product\Variant;

use App\Extensions\Grido\IRepositorySource;
use App\Product\Product;
use Nette\Database\Table\ActiveRow;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class VariantRepository extends BaseRepository implements IRepositorySource
{


    /** @var string */
    protected $entityName = Variant::class;



    /**
     * @param $id int
     * @return Variant
     * @throws VariantNotFoundException
     */
    public function getOneById(int $id) : Variant
    {
        $filters['where'][] = ['id', '=', $id];
        $result = $this->findOneBy($filters);
        if (!$result) {
            throw new VariantNotFoundException(sprintf('Varianta s id \'%d\' nebyla nalezena.', $id));
        }
        return $result;
    }



    /**
     * Find by product id.
     * @param $id int
     * @return Variant[]|array
     */
    public function findByProductId(int $id) : array
    {
        $filters['where'][] = ['productId', '=', $id];
        return $this->findBy($filters) ?: [];
    }



    /**
     * @param $productId int
     * @param $parameterGroupId int
     * @return Variant[]|array
     */
    public function findByProductIdAndParameterGroupId(int $productId, int $parameterGroupId) : array
    {
        $filters['where'][] = ['productId', '=', $productId];
        $filters['where'][] = ['parameterGroupId', '=', $parameterGroupId];
        return $this->findBy($filters) ?: [];
    }



    /**
     * @param $id int
     * @return Variant[]|array
     */
    public function findPublishedVariantsByProductId(int $id) : array
    {
        $productAnnotation = Product::getAnnotation();
        $subQuery = sprintf('(SELECT %s FROM %s WHERE %s = \'%s\')',
            $productAnnotation->getPropertyByName('id')->getColumn()->getName(),
            $productAnnotation->getTable()->getName(),
            $productAnnotation->getPropertyByName('state')->getColumn()->getName(),
            Product::PUBLISH);

        $filter['where'][] = ['productId', '=', $id];
        $filter['where'][] = ['productVariantId', 'IN.SQL', $subQuery];

        return $this->findBy($filter) ?: [];
    }



    /**
     * Find by product variant id.
     * @param $id int
     * @return Variant|NULL
     */
    public function findOneByProductVariantId(int $id)
    {
        $filters['where'][] = ['productVariantId', '=', $id];
        return $this->findOneBy($filters) ?: NULL;
    }



    /**
     * @param $id int
     * @return Variant|null
     */
    public function findOneByProductId(int $id)
    {
        $filters['where'][] = ['productId', '=', $id];
        return $this->findOneBy($filters) ?: NULL;
    }



    /**
     * @param $productId int
     * @param $productVariantId int
     * @param $parameterId int
     * @param $parentVariantId int|null
     * @return Variant|null
     */
    public function findOneByProductIdAndProductVariantIdAndProductVariantParameterIdAndParentVariantId(int $productId,
                                                                                                        int $productVariantId,
                                                                                                        int $parameterId,
                                                                                                        int $parentVariantId = NULL)
    {
        $filters['where'][] = ['productId', '=', $productId];
        $filters['where'][] = ['productVariantId', '=', $productVariantId];
        $filters['where'][] = ['productVariantParameterId', '=', $parameterId];
        $parentVariantId !== NULL && $filters['where'][] = ['parentVariantId', '=', $parentVariantId];

        return $this->findOneBy($filters) ?: NULL;
    }



    /**
     * @param $productId int
     * @return array
     */
    public function findJoinedByProductId(int $productId) : array
    {
        $productAnnotation = Product::getAnnotation();
        $codeProperty = $productAnnotation->getPropertyByName('code');

        $filters['sort'] = [$codeProperty->getColumn()->getName(), 'ASC'];
        $filters['where'][] = ['productId', '=', $productId];
        return $this->findJoined($filters);
    }



    /**
     * @return array
     */
    public function findAllGrouped() : array
    {
        $key = $this->createCacheKey(__FUNCTION__);
        return $this->runCachedQuery($key, function (VariantRepository $repository) {
            $filters['columns'] = ['id', 'productId', 'productParameterId', 'productVariantId', 'parameterGroupId', 'productVariantParameterId', 'parentVariantId'];
            return $repository->getEntityMapper()
                ->getQueryManager($repository->getEntityName())
                ->findBy($filters, function ($results) {
                    $list = [];
                    /** @var $results ActiveRow[] */
                    foreach ($results as $result) {
                        $resultArray = $result->toArray();
                        $list['variantList']['all'][$result['pv_id']] = $resultArray;
                        $list['variantList']['byProductVariant'][$result['pv_product_variant_id']] = $result['pv_product_id'];
                        $list['variantList']['byMainProduct'][$result['pv_product_id']][] = $result['pv_product_variant_id'];
                        $list['groups'][$result['pv_product_id']][$result['pv_parameter_group_id']][] = $resultArray;
                    }
                    return $list;
                }) ?: [];
        });
    }



    /**
     * Find joined.
     * @param $filters array
     * @return array
     */
    public function findJoined(array $filters) : array
    {
        $filters['join'] = $this->getVariantJoins();
        $mapping = function ($results) {
            return $results;
        };

        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy($filters, $mapping) ?: [];
    }



    /**
     * Find joined.
     * @param $filters array
     * @return array
     */
    public function findParentJoined(array $filters) : array
    {
        $filters['join'] = $this->getParentVariantJoins();
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
        $filters['join'] = $this->getVariantJoins();
        return $this->count($filters);
    }



    /**
     * @param $filters array
     * @return CountDTO
     */
    public function countParentJoined(array $filters)
    {
        $filters['join'] = $this->getParentVariantJoins();
        return $this->count($filters);
    }



    /**
     * todo názvy nahradit daty z entity
     * @return array
     */
    protected function getVariantJoins() : array
    {
        return [
            ['LEFT JOIN', 'product', 'p_id = pv_product_variant_id'],
            ['LEFT JOIN', 'product_translation', 'pt_product_id = p_id'],
            ['LEFT JOIN', 'product_parameter_group', 'ppg_id = pv_parameter_group_id'],
            ['LEFT JOIN', 'product_parameter_group_translation', 'ppgt_product_parameter_group_id = ppg_id'],
            ['LEFT JOIN', 'product_parameter', 'pp_id = pv_product_variant_parameter_id'],
            ['LEFT JOIN', 'product_parameter_translation', 'ppt_product_parameter_id = pp_id']
        ];
    }



    /**
     * todo názvy nahradit daty z entity
     * @return array
     */
    protected function getParentVariantJoins() : array
    {
        return [
            ['LEFT JOIN', 'product', 'p_id = pv_product_id'],
            ['LEFT JOIN', 'product_translation', 'pt_product_id = p_id'],
            ['LEFT JOIN', 'product_parameter_group', 'ppg_id = pv_parameter_group_id'],
            ['LEFT JOIN', 'product_parameter_group_translation', 'ppgt_product_parameter_group_id = ppg_id'],
            ['LEFT JOIN', 'product_parameter', 'pp_id = pv_product_variant_parameter_id'],
            ['LEFT JOIN', 'product_parameter_translation', 'ppt_product_parameter_id = pp_id']
        ];
    }
}