<?php

declare(strict_types = 1);

namespace App\Category;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use App\Url\IUrlRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryRepository extends BaseRepository implements IRepository, IRepositorySource, IUrlRepository
{


    /** @var string */
    protected $entityName = CategoryEntity::class;



    /**
     * @param $url string
     * @param $languageId int
     * @return CategoryEntity
     * @throws CategoryNotFoundException
     */
    public function getOnePublishedByUrlAndLanguageId(string $url, int $languageId) : CategoryEntity
    {
        $filter['where'][] = ['url', '=', $url];
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['status', '=', CategoryEntity::PUBLISH];
        $result = $this->findOneBy($filter);
        if (!$result) {
            throw new CategoryNotFoundException('Category not found.');
        }
        return $result;
    }



    /**
     * @param int $id
     * @return CategoryEntity
     * @throws CategoryNotFoundException
     */
    public function getOneById(int $id) : CategoryEntity
    {
        $result = $this->findOneBy([
            "where" => [
                ["id", "=", $id]
            ]
        ]);

        if (!$result) {
            throw new CategoryNotFoundException(sprintf("Kategorie s id '%s' nebyla nalezena.", $id));
        }

        return $result;
    }



    /**
     * @param $id array
     * @return CategoryEntity[]|array
     */
    public function findByMoreId(array $id) : array
    {
        $filter['where'][] = ['id', '', $id];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $id
     * @return CategoryEntity[]|array
     */
    public function findPublishedByMoreId(array $id) : array
    {
        $filter['where'][] = ['id', '', $id];
        $filter['where'][] = ['status', '=', CategoryEntity::PUBLISH];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param int $languageId
     * @param string $name
     * @return CategoryEntity|null
     */
    public function findOneByLanguageIdAndName(int $languageId, string $name)
    {
        return $this->findOneBy([
            "where" => [
                ["languageId", "=", $languageId],
                ["name", "=", $name]
            ]
        ]);
    }



    /**
     * @param $languageId int
     * @param $query string
     * @return CountDTO
     */
    public function countPublishedByLanguageIdAndSearch(int $languageId, string $query) : CountDTO
    {
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['name', 'LIKE', '%' . $query . '%'];
        $filter['where'][] = ['status', '=', CategoryEntity::PUBLISH];
        return $this->count($filter);
    }



    /**
     * @param $languageId int
     * @param $query string
     * @param $limit int
     * @param $offset int
     * @return CategoryEntity[]|array
     */
    public function findPublishedByLanguageIdAndSearch(int $languageId, string $query, int $limit, int $offset) : array
    {
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['name', 'LIKE', '%' . $query . '%'];
        $filter['where'][] = ['status', '=', CategoryEntity::PUBLISH];
        $filter['limit'] = $limit;
        $filter['offset'] = $offset;
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param int $languageId
     * @param string $url
     * @return mixed
     */
    public function findOneByLanguageIdAndUrl(int $languageId, string $url)
    {
        return $this->findOneBy([
            "where" => [
                ["languageId", "=", $languageId],
                ["url", "=", $url]
            ]
        ]);
    }



    /**
     * @param int $languageId
     * @return CategoryEntity[]|array
     */
    public function findByLanguageId(int $languageId) : array
    {
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        $filter['where'][] = ['languageId', '=', $languageId];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param int $languageId
     * @return CategoryEntity[]|array
     */
    public function findByLanguageIdSortedByName(int $languageId) : array
    {
        $filter['sort'] = ['name', 'ASC'];
        $filter['where'][] = ['languageId', '=', $languageId];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param int $languageId
     * @return CategoryEntity[]|array
     */
    public function findByLanguageIdWithoutParentCategoryId(int $languageId) : array
    {
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['parentCategoryId', '', NULL];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $parentId int
     * @return CategoryEntity[]|array
     */
    public function findByParentCategoryId(int $parentId) : array
    {
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        $filter['where'][] = ['parentCategoryId', '=', $parentId];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $languageId int|null
     * @param $categoryId int|null
     * @return CategoryEntity[]|null
     */
    public function findByLanguageIdWithoutCategoryId(int $languageId, int $categoryId = NULL)
    {
        $where = [["languageId", "=", $languageId]];
        if ($categoryId) {
            $where[] = ["id", "!=", $categoryId];
        }

        return $this->findBy([
            "where" => $where,
            "sort" => [
                ["name"],
                "ASC"
            ]
        ]);
    }



    /**
     * @param $languageId int
     * @return CategoryEntity[]|array
     */
    public function findPublishedByLanguageId(int $languageId) : array
    {
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['status', '=', CategoryEntity::PUBLISH];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $languageId int
     * @return CategoryEntity[]|array
     */
    public function findPublishedForHomepageByLanguageId(int $languageId) : array
    {
        $filter['sort'] = [['LENGTH(homepageSort)', 'homepageSort'], 'ASC'];
        $filter['where'][] = ['showOnHomepage', '=', TRUE];
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['status', '=', CategoryEntity::PUBLISH];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $languageId int
     * @return CategoryEntity[]|array
     */
    public function findForHomepage(int $languageId) : array
    {
        $filter['sort'] = [['LENGTH(homepageSort)', 'homepageSort'], 'ASC'];
        $filter['where'][] = ['showOnHomepage', '=', TRUE];
        $filter['where'][] = ['languageId', '=', $languageId];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $languageId int
     * @return CategoryEntity[]|array
     */
    public function findForCategorySliderByLanguageId(int $languageId) : array
    {
        $filter['sort'] = [['LENGTH(categorySliderSort)', 'categorySliderSort'], 'ASC'];
        $filter['where'][] = ['categorySlider', '=', TRUE];
        $filter['where'][] = ['languageId', '=', $languageId];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $languageId int
     * @return CategoryEntity[]|array
     */
    public function findPublishedForCategorySliderByLanguageId(int $languageId) : array
    {
        $filter['sort'] = [['LENGTH(categorySliderSort)', 'categorySliderSort'], 'ASC'];
        $filter['where'][] = ['categorySlider', '=', TRUE];
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['status', '=', CategoryEntity::PUBLISH];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $url
     * @param $languageId int
     * @return CategoryEntity|null
     */
    public function findOneByUrlAndLanguageId(string $url, int $languageId)
    {
        return $this->findOneByLanguageIdAndUrl($languageId, $url) ?: NULL;
    }



    /**
     * @param $id int
     * @return CategoryEntity[]|array
     */
    public function findTopPublishedByParentId(int $id) : array
    {
        $filter['where'][] = ['top', '=', TRUE];
        $filter['where'][] = ['parentCategoryId', '=', $id];
        $filter['where'][] = ['status', '=', CategoryEntity::PUBLISH];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $id int
     * @return CategoryEntity[]|array
     */
    public function findPublishedByParentId(int $id) : array
    {
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        $filter['where'][] = ['parentCategoryId', '=', $id];
        $filter['where'][] = ['status', '=', CategoryEntity::PUBLISH];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $id int
     * @return CategoryEntity|null
     */
    public function findOnePublishedById(int $id)
    {
        $filter['where'][] = ['id', '=', $id];
        $filter['where'][] = ['status', '=', CategoryEntity::PUBLISH];
        return $this->findOneBy($filter) ?: NULL;
    }



    /**
     * @return CategoryEntity[]|array
     */
    public function findWithProductSorter() : array
    {
        $filter['where'][] = ['productSorter', 'NOT', NULL];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @return array
     */
    public function findList() : array
    {
        $categoryAnnotation = CategoryEntity::getAnnotation();

        $filter['sort'] = ['name', 'ASC'];
        $filter['columns'] = ['id', 'name'];

        return $this->getEntityMapper()
            ->getQueryManager(CategoryEntity::class)
            ->findBy($filter, function ($result) use ($categoryAnnotation) {
                $list = [];
                foreach ($result as $row) {
                    $id = $row[$categoryAnnotation->getPropertyByName('id')->getColumn()->getName()];
                    $name = $row[$categoryAnnotation->getPropertyByName('name')->getColumn()->getName()];
                    $list[$id] = $name;
                }
                return $list;
            }) ?: [];
    }
}