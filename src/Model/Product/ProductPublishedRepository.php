<?php

declare(strict_types = 1);

namespace App\Product;

use App\FrontModule\Components\Category\Filtration\Filter\PriceRange;
use App\FrontModule\Components\Category\Filtration\Filter\SortFilter;
use App\Product\Parameter\ProductParameter;
use App\Product\Translation\ProductTranslation;
use App\ShoppingCart\ShoppingCartTranslation;
use Kdyby\Translation\ITranslator;
use Nette\Caching\IStorage;
use Nette\Database\Connection;
use Ricaefeliz\Mappero\Mappero;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\Repositories\Traits\ReadOnlyTrait;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductPublishedRepository extends BaseRepository
{


    public function __construct(
        IStorage $storage,
        Mappero $mappero,
        Connection $connection
    ) {
        $this->entityMapper = $mappero;
        $this->storage = $storage;
        $this->pdo = $connection->getPdo();
    }


    /** @var string */
    const SORT_STOCK = 'stock';

    use ReadOnlyTrait;

    /** @var string */
    protected $entityName = Product::class;

    /** @var \PDO */
    protected $pdo;

    /**
     * @param int $id
     * @param ITranslator|null $translator
     * @return Product
     * @throws ProductNotFoundException
     */
    public function getOneById(int $id, ITranslator $translator = NULL) : Product
    {
        $product = $this->findOneBy([
            'where' => [
                ['id', '=', $id],
                $this->getCondition()
            ]
        ]);
        if (!$product) {
            $message = $translator ? $translator->translate(sprintf('%s.product.not.found', ShoppingCartTranslation::getFileName())) : 'Product not found.';
            throw new ProductNotFoundException($message);
        }
        return $product;
    }


    /**
     * @param int $id
     * @return Product|null
     */
    public function getOneByIdNoE(int $id) : ?Product
    {
        $product = $this->findOneBy([
            'where' => [
                ['id', '=', $id],
                $this->getCondition()
            ]
        ]);
        if (!$product) {
						return null;
        }
        return $product;
    }
		

    /**
     * @param $url string
     * @param ITranslator|null $translator
     * @return Product
     * @throws ProductNotFoundException
     */
    public function getOneByUrl(string $url, ITranslator $translator = NULL) : Product
    {
        $productTranslation = ProductTranslation::getAnnotation();
        $subQuery = sprintf('(SELECT %s FROM %s WHERE %s = \'%s\')', $productTranslation->getPropertyByName('productId')->getColumn()->getName(),
            $productTranslation->getTable()->getName(), $productTranslation->getPropertyByName('url')->getColumn()->getName(), $url);

        $product = $this->findOneBy([
            'where' => [
                ['id', 'IN.SQL', $subQuery],
                $this->getCondition()
            ]
        ]);
        if (!$product) {
            $message = $translator !== NULL ? $translator->translate(sprintf('%s.product.not.found', ShoppingCartTranslation::getFileName())) : 'Product not found.';
            throw new ProductNotFoundException($message);
        }
        return $product;
    }



    /**
     * @param $productId array
     * @param $groupId int
     * @return array
     */
    public function findProductIdWithNonGroupedVariantsFromGroupByMoreProductIdAndGroupId(array $productId, int $groupId) : array
    {
        //find variants
        $parameterSubquery = sprintf('(SELECT pv_product_variant_id FROM product_variant
        	WHERE pv_product_variant_id IN (SELECT p_id FROM product WHERE p_state = \'%s\')
        	%s 
        	GROUP BY pv_product_id, pv_parameter_group_id)',
            Product::PUBLISH,
            sprintf(' AND pv_product_id NOT IN (SELECT pv_product_id FROM product_variant WHERE pv_parameter_group_id = \'%d\') ', $groupId));

        $filter['columns'] = ['id'];
        $filter['where'][] = ['id', 'IN', $productId];
        $filter['where'][] = ['id', 'NOTIN.SQL', $parameterSubquery];
        $filter['where'][] = $this->getCondition();

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
     * @param $productId int[]
     * @param $variantParameterId int[]|array [parameterId => groupId,..]
     * @return array return main products or products with parameters in $variantParameterId
     */
    public function findProductIdWithGroupedVariantsByProductId(array $productId, array $variantParameterId = []) : array
    {
        //find variants
        $parameterSubquery = sprintf('(SELECT pv_product_variant_id FROM product_variant
        	WHERE pv_product_variant_id IN (SELECT p_id FROM product WHERE p_state = \'%s\')
        	%s 
        	GROUP BY pv_product_id, pv_parameter_group_id)',
            Product::PUBLISH,
            $variantParameterId ? sprintf('AND (pv_parameter_group_id IN (%s) AND pv_product_variant_parameter_id NOT IN (%s) OR pv_parameter_group_id NOT IN (%1$s) ) ',
                implode(array_values($variantParameterId), ','), implode(array_keys($variantParameterId), ',')) : '');

        $filter['columns'] = ['id'];
        $filter['where'][] = ['id', 'IN', $productId];
        $filter['where'][] = ['id', 'NOTIN.SQL', $parameterSubquery];
        $filter['where'][] = $this->getCondition();

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
        $filter['where'][] = $this->getCondition();

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
     * @param $productId array
     * @param $filter array
     * @return array
     */
    public function findProductIdByProductIdAndFilters(array $productId, array $filter = []) : array
    {
        //general filters
        $_filter['columns'] = ['id'];
        $_filter['where'][] = ['id', '', $productId];
        $_filter['where'][] = $this->getCondition();

        //optional filters
        isset($filter['stock']) && $filter['stock'] === TRUE ? $_filter['where'][] = ['stock', '>', '0'] : NULL;
        isset($filter['priceFrom']) ? $_filter['where'][] = ['price', '>=', $filter['priceFrom']] : NULL;
        isset($filter['priceTo']) ? $_filter['where'][] = ['price', '<=', $filter['priceTo']] : NULL;
        if (isset($filter['groupedProductParameters'])) {
            $i = 0;
            $productParameterRelation = ProductParameter::getAnnotation();
            $subQuery = '(';
            foreach ($filter['groupedProductParameters'] as $groupId => $groupParameters) {
                $subQuery .= $i > 0 ? sprintf(' AND %s IN (', $productParameterRelation->getPropertyByName('productId')->getColumn()->getName()) : '';
                $subQuery .= sprintf('SELECT %s FROM %s WHERE %s IN (\'%s\')',
                    $productParameterRelation->getPropertyByName('productId')->getColumn()->getName(),
                    $productParameterRelation->getTable()->getName(),
                    $productParameterRelation->getPropertyByName('parameterId')->getColumn()->getName(),
                    implode('\',\'', $groupParameters));
                $subQuery .= $i > 0 ? ')' : '';
                $i++;
            }
            $subQuery .= ')';
            $_filter['where'][] = ['id', 'IN.SQL', $subQuery];
        }

        $result = $this->getEntityMapper()
            ->getQueryManager(Product::class)
            ->findBy($_filter, function ($rows) {
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
     * @param $productId array
     * @return array
     */
    public function findMinAndMaxPriceByMoreProductId(array $productId) : array
    {
        $productAnnotation = Product::getAnnotation();
        $price = $productAnnotation->getPropertyByName('price')->getColumn();

        $filter['where'][] = ['id', '', $productId];
        $filter['columns'] = ['MIN(' . $price->getName() . ') AS min', 'MAX(' . $price->getName() . ') AS max'];
        $result = $this->getEntityMapper()
            ->getQueryManager(Product::class)
            ->findOneBy($filter, function ($rows) {
                return ['min' => $rows['min'], 'max' => $rows['max']];
            });
        return $result;
    }



    /**
     * @return null|Product[]
     */
    public function findByLimit(int $limit, int $offset = NULL)
    {
        return $this->findBy([
            'where' => [
                $this->getCondition()
            ], 'limit' => $limit,
            'offset' => $offset
        ]);
    }



    /**
     * @param $productId array
     * @return Product[]|array
     */
    public function findInStockByMoreId(array $productId) : array
    {
        $filters['where'][] = ['id', '', $productId];
        $filters['where'][] = ['stock', '>', 0];
        $filters['where'][] = $this->getCondition();
        return $this->findBy($filters) ?: [];
    }



    /**
     * @return array
     */
    public function findStock() : array
    {
        $key = $this->createCacheKey(__FUNCTION__);
        return $this->runCachedQuery($key, function (ProductPublishedRepository $repository) {
            $productAnnotation = Product::getAnnotation();
            $idColumnName = $productAnnotation->getPropertyByName('id')->getColumn()->getName();
            $stockColumnName = $productAnnotation->getPropertyByName('stock')->getColumn()->getName();

            $filters['columns'] = ['id', 'stock'];
            $filters['where'][] = $this->getCondition();

            return $repository->getEntityMapper()
                ->getQueryManager($repository->getEntityName())
                ->findBy($filters, function ($result) use ($idColumnName, $stockColumnName) {
                    $list = [];
                    foreach ($result as $row) {
                        $list[$row[$idColumnName]] = $row[$stockColumnName];
                    }
                    return $list;
                }) ?: [];
        });
    }



    /**
     * Find by more id.
     * @param $id array
     * @return array|Product[]
     */
    public function findByMoreId(array $id) : array
    {
        $filters['where'][] = ['id', '', $id];
        $filters['where'][] = $this->getCondition();
        return $this->findBy($filters) ?: [];
    }



    /**
     * @param $id array
     * @param $offset int
     * @param $limit int
     * @param $filter array
     * @return Product[]|array
     */
    public function findByMoreIdAndOffsetAndLimit(array $id, int $offset, int $limit, array $filter = []) : array
    {
        $filters['limit'] = $limit;
        $filters['offset'] = $offset;
        $filters['where'][] = ['id', '', $id];
        $filters['where'][] = $this->getCondition();
        if (isset($filter[SortFilter::KEY])) {
            if (in_array($filter[SortFilter::KEY], [SortFilter::SORT_CHEAPEST, SortFilter::SORT_MOST_EXPENSIVE], TRUE)) {
                $filters[SortFilter::KEY] = ['price', $filter[SortFilter::KEY] === SortFilter::SORT_CHEAPEST ? 'ASC' : 'DESC'];
            } elseif ($filter[SortFilter::KEY] === SortFilter::SORT_IN_STOCK) { // is in stock
                $filters[SortFilter::KEY] = ['p_stock > 0 DESC, p_new_until_to IS NOT NULL AND p_new_until_to >= now()', 'DESC'];
            } elseif ($filter[SortFilter::KEY] === self::SORT_STOCK) { // number of items in stock
                $filters[SortFilter::KEY] = ['stock', 'DESC'];
            } elseif (is_array($filter[SortFilter::KEY]) && !empty($filter[SortFilter::KEY])) {
                //array_reverse because FIELD function sorting unknown product ahead and this is a trick
                //how to sort ahead known id and than these unknown
                $filters[SortFilter::KEY] = [sprintf('FIELD(p_id, %s)', implode(',', array_reverse($filter[SortFilter::KEY]))), 'DESC'];
            }
        }
        return $this->findBy($filters) ?: [];
    }



    /**
     * @return CountDTO
     */
    public function getCount() : CountDTO
    {
        return $this->count([
            'where' => [
                $this->getCondition()
            ]]);
    }



    /**
     * @param $languageId int
     * @param $query string
     * @param $filter array
     * @return array
     */
    public function findMoreIdBySearch(int $languageId, string $query, array $filter = []) : array
    {
        $_filter['whereOr'] = $this->getWhereOrConditionForSearch($languageId, $query);
        $_filter['where'][] = $this->getCondition();
        $_filter['columns'] = ['id'];

        if (isset($filter[PriceRange::PRICE_FROM_KEY])) {
            $_filter['where'][] = ['price', '>=', $filter[PriceRange::PRICE_FROM_KEY]];
        }
        if (isset($filter[PriceRange::PRICE_TO_KEY])) {
            $_filter['where'][] = ['price', '<=', $filter[PriceRange::PRICE_TO_KEY]];
        }
        if (isset($filter['stock'])) {
            $_filter['where'][] = ['stock', '>', 0];
        }
        if (isset($filter['sort'])) {
            $_filter['sort'] = ['price', $filter['sort'] === SortFilter::SORT_CHEAPEST ? 'ASC' : 'DESC'];
        }

        return $this->getEntityMapper()
            ->getQueryManager(Product::class)
            ->findBy($_filter, function ($rows) {
                $result = [];
                $productAnnotation = Product::getAnnotation();
                foreach ($rows as $row) {
                    $result[] = $row[$productAnnotation->getPropertyByName('id')->getColumn()->getName()];
                }
                return $result;
            }) ?: [];
    }



    /**
     * @param $languageId int
     * @param $query string
     * @return array
     */
    private function getWhereOrConditionForSearch(int $languageId, string $query) : array
    {
        $productTranslationAnnotation = ProductTranslation::getAnnotation();
        $subQuery = sprintf('(SELECT %s FROM %s WHERE %s LIKE %s AND %s = \'%s\')',
            $productTranslationAnnotation->getPropertyByName('productId')->getColumn()->getName(),
            $productTranslationAnnotation->getTable()->getName(),
            $productTranslationAnnotation->getPropertyByName('name')->getColumn()->getName(),
            $this->pdo->quote("%$query%"),
            $productTranslationAnnotation->getPropertyByName('languageId')->getColumn()->getName(),
            $languageId);

        return [
            ['code', 'LIKE', '%' . $query . '%'],
            ['id', 'IN.SQL', $subQuery],
        ];
    }



    /**
     * Special method for product feed.
     * @param $limit int
     * @param $offset int|null
     * @return Product[]|array
     */
    public function findForProductFeed(int $limit, int $offset = NULL) : array
    {
        return $this->findBy([
            'where' => $this->getWhereFilterForProductFeed(),
            'limit' => $limit,
            'offset' => $offset
        ]) ?: [];
    }



    /**
     * Special method for product feed.
     * @return CountDTO
     */
    public function getCountForProductFeed() : CountDTO
    {
        return $this->count([
            'where' => $this->getWhereFilterForProductFeed()
        ]);
    }



    /**
     * @return Product[]|array
     */
    public function findCompletedForGeneratePhotoName() : array
    {
        $productTranslationAnnotation = ProductTranslation::getAnnotation();
        $subQuery = sprintf('(SELECT %s FROM %s WHERE %s = \'0\')',
            $productTranslationAnnotation->getPropertyByName('productId')->getColumn()->getName(),
            $productTranslationAnnotation->getTable()->getName(),
            $productTranslationAnnotation->getPropertyByName('photoNameGenerated')->getColumn()->getName());

        $filter['where'][] = $this->getCondition();
        $filter['where'][] = ['completed', '=', TRUE];
        $filter['where'][] = ['photo', 'NOT', NULL];
        $filter['where'][] = ['id', 'IN.SQL', $subQuery];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @return array
     */
    protected function getWhereFilterForProductFeed() : array
    {
        return [
            $this->getCondition(),
            ['saleOnline', '=', TRUE],
        ];
    }



    /**
     * @return array
     */
    private function getCondition() : array
    {
        return ['state', '=', Product::PUBLISH];
    }
}