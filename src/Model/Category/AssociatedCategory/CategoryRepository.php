<?php

declare(strict_types = 1);

namespace App\Category\AssociatedCategory;

use App\Extensions\Grido\IRepositorySource;
use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryRepository extends BaseRepository implements IRepositorySource
{


    /** @var string */
    protected $entityName = Category::class;



    /**
     * @param $id int
     * @return Category
     * @throws NotFoundException
     */
    public function getOneById(int $id) : Category
    {
        $filter['where'][] = ['id', '=', $id];
        $category = $this->findOneBy($filter);
        if (!$category) {
            throw new NotFoundException('Category not found.');
        }
        return $category;
    }



    /**
     * @param $categoryId int
     * @return Category[]|array
     */
    public function findByCategoryId(int $categoryId) : array
    {
        $filter['where'][] = ['categoryId', '=', $categoryId];

        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $categoryId int
     * @param $associatedCategoryId int
     * @return Category|null
     */
    public function findOneByCategoryIdAndCategoryAssociatedId(int $categoryId, int $associatedCategoryId)
    {
        $filter['where'][] = ['categoryId', '=', $categoryId];
        $filter['where'][] = ['associatedCategoryId', '=', $associatedCategoryId];

        return $this->findOneBy($filter);
    }



    /**
     * @param $filters array
     * @return array
     */
    public function findJoined(array $filters) : array
    {
        $filters['join'] = $this->getRelatedJoins();

        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy($filters, function ($results) {
                return $results;
            }) ?: [];
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
            ['LEFT JOIN', 'category', 'cat_id = cac_associated_category_id'],
        ];
    }
}