<?php

declare(strict_types = 1);

namespace App\Category\Product\Sorting;

use Nette\Caching\IStorage;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Mappero;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SortingRepository extends BaseRepository
{


    /**
     * @var Context
     * todo implement to Mappero
     */
    protected $database;

    /** @var string */
    protected $entityName = Sorting::class;



    public function __construct(Context $context,
                                IStorage $storage,
                                Mappero $mappero)
    {
        parent::__construct($storage, $mappero);
        $this->database = $context;
    }



    /**
     * @param $categoryId int
     * @return int[]|array
     */
    public function findProductIdByCategoryId(int $categoryId) : array
    {
        $filter['columns'] = ['productId'];
        $filter['where'][] = ['categoryId', '=', $categoryId];
        $filter['sort'] = [['LENGTH(sorting)', 'sorting'], 'ASC'];

        return $this->getEntityMapper()
            ->getQueryManager(Sorting::class)
            ->findBy($filter, function ($result) {
                $id = [];
                foreach ($result as $row) {
                    $id[] = $row['cps_product_id'];
                }
                return $id;
            }) ?: [];
    }



    /**
     * @param $categoryId int
     * @return void
     */
    public function deleteByCategoryId(int $categoryId)
    {
        $annotation = Sorting::getAnnotation();
        $categoryIdColumn = $annotation->getPropertyByName('categoryId');

        $sql = sprintf('DELETE FROM %s WHERE %s = ?', $annotation->getTable()->getName(), $categoryIdColumn->getColumn()->getName());
        $this->database->query($sql, $categoryId);
    }



    /**
     * @param $productId int[]
     * @param $categoryId int
     * @return void
     */
    public function deleteByMoreProductIdAndCategoryId(array $productId, int $categoryId)
    {
        $annotation = Sorting::getAnnotation();
        $productIdColumn = $annotation->getPropertyByName('productId');
        $categoryIdColumn = $annotation->getPropertyByName('categoryId');

        $sql = sprintf('DELETE FROM %s WHERE %s IN (?) AND %s = ?', $annotation->getTable()->getName(), $productIdColumn->getColumn()->getName(), $categoryIdColumn->getColumn()->getName());
        $this->database->queryArgs($sql, [$productId, $categoryId]);
    }
}