<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\CategoryFiltration\CategoryFiltrationEntity;
use App\Extensions\Grido\IRepositorySource;
use App\Helpers\Entities;
use App\IRepository;
use App\Url\IUrlRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterTranslationRepository extends BaseRepository implements IRepositorySource, IRepository, IUrlRepository
{


    /** @var string */
    protected $entityName = ProductParameterTranslationEntity::class;



    /**
     * @param array $productParameterId
     * @param int $languageId
     * @param string $value
     * @return mixed
     */
    public function findOneByProductParameterIdAndLanguageIdAndValue(array $productParameterId, int $languageId, string $value)
    {
        return $this->findOneBy([
            "where" => [
                ["productParameterId", "", $productParameterId],
                ["languageId", "=", $languageId],
                ["value", "=", $value]
            ]
        ]);
    }



    /**
     * @param int $languageId
     * @return ProductParameterTranslationEntity[]|null
     */
    public function findByLanguageId(int $languageId)
    {
        return $this->findBy([
            "where" => [
                ["languageId", "=", $languageId]
            ]
        ]);
    }



    /**
     * @param int $id
     * @return mixed
     * @throws ProductParameterTranslationNotFoundException
     */
    public function getOneById(int $id)
    {
        $result = $this->findOneBy([
            "where" => [
                ["id", "=", $id]
            ]
        ]);

        if (!$result) {
            throw new ProductParameterTranslationNotFoundException(sprintf("Překlad parametru s id '%s' nebyl nalezen.", $id));
        }

        return $result;
    }



    /**
     * @param array $parametersId
     * @return array
     * @throws ProductParameterTranslationNotFoundException
     * @throws \InvalidArgumentException
     */
    public function getByParametersId(array $parametersId) : array
    {
        if (!$parametersId) {
            throw new \InvalidArgumentException('Prázdné pole s id parametrů.');
        }
        $result = $this->findBy([
            'where' => [
                ['productParameterId', '', $parametersId]
            ]
        ]);
        $diff = array_diff($parametersId, $result ? Entities::getProperty($result, 'productParameterId') : []);
        if ($diff) {
            throw new ProductParameterTranslationNotFoundException(sprintf('Překlady parametrů s id "%s" se nepodařilo nalézt.', implode(',', $diff)));
        }
        return $result;
    }



    /**
     * @param $groupId int
     * @param $languageId int
     * @return ProductParameterTranslationEntity[]|array
     */
    public function findByGroupIdAndLanguageId(int $groupId, int $languageId) : array
    {
        $productParameterAnnotation = ProductParameterEntity::getAnnotation();
        $table = $productParameterAnnotation->getTable();
        $idColumn = $productParameterAnnotation->getPropertyByName('id')->getColumn();
        $groupIdColumn = $productParameterAnnotation->getPropertyByName('productParameterGroupId')->getColumn();

        $filters['where'][] = ['productParameterId', 'IN.SQL', sprintf('(SELECT %s FROM %s WHERE %s = \'%d\')', $idColumn->getName(), $table->getName(), $groupIdColumn->getName(), $groupId)];
        $filters['where'][] = ['languageId', '=', $languageId];
        $filters['sort'] = ['value', 'ASC'];
        return $this->findBy($filters) ?: [];
    }



    /**
     * Find list by set category filtration.
     * @param $categoryId int
     * @param $languageId int
     * @return array
     */
    public function findListByCategoryIdAndLanguageId(int $categoryId, int $languageId) : array
    {
        $sql = sprintf('(SELECT pp_id FROM product_parameter WHERE pp_product_parameter_group_id IN 
                        (SELECT cf_product_parameter_group_id FROM category_filtration WHERE cf_category_id = \'%s\') )', $categoryId);
        $filters['where'][] = ['productParameterId', 'IN.SQL', $sql];
        $filters['where'][] = ['languageId', '=', $languageId];
        $filters['columns'] = ['productParameterId', 'value'];
        $filters['sort'] = ['value', 'ASC'];

        return $this->getEntityMapper()
            ->getQueryManager(ProductParameterTranslationEntity::class)
            ->findBy($filters, function ($rows) {
                $result = [];
                foreach ($rows as $row) {
                    $result[$row['ppt_product_parameter_id']] = $row['ppt_value'];
                }
                return $result;
            }) ?: [];
    }



    /**
     * Find parameter translations which are available in category
     * @param $url array
     * @param $languageId int
     * @param $categoryId int
     * @return ProductParameterTranslationEntity[]|array
     */
    public function findByMoreUrlAndLanguageIdAndCategoryId(array $url, int $languageId, int $categoryId) : array
    {
        $categoryFiltrationAnnotation = CategoryFiltrationEntity::getAnnotation();
        $productParameterAnnotation = ProductParameterEntity::getAnnotation();

        $subQuery = sprintf('(SELECT %s
        FROM %s
        WHERE %s
        IN (
            SELECT %s
            FROM %s
            WHERE %s = \'%s\')
            )',
            $productParameterAnnotation->getPropertyByName('id')->getColumn()->getName(),
            $productParameterAnnotation->getTable()->getName(),
            $productParameterAnnotation->getPropertyByName('productParameterGroupId')->getColumn()->getName(),
            $categoryFiltrationAnnotation->getPropertyByName('productParameterGroupId')->getColumn()->getName(),
            $categoryFiltrationAnnotation->getTable()->getName(),
            $categoryFiltrationAnnotation->getPropertyByName('categoryId')->getColumn()->getName(),
            $categoryId);

        $filter['sort'] = ['productParameterId', 'ASC'];
        $filter['where'][] = ['url', '', $url];
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['productParameterId', 'IN.SQL', $subQuery];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $url string
     * @param $languageId int
     * @return ProductParameterEntity|null
     */
    public function findOneByUrlAndLanguageId(string $url, int $languageId)
    {
        $filter['where'][] = ['url', '=', $url];
        $filter['where'][] = ['languageId', '=', $languageId];
        return $this->findOneBy($filter) ?: NULL;
    }


}