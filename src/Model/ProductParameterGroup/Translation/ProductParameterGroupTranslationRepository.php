<?php

namespace App\ProductParameterGroup;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use App\Product\Parameter\ProductParameter;
use App\ProductParameter\ProductParameterEntity;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupTranslationRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = ProductParameterGroupTranslationEntity::class;



    /**
     * @param int $id
     * @return ProductParameterGroupTranslationEntity
     * @throws ProductParameterGroupTranslationNotFoundException
     */
    public function getOneById(int $id) : ProductParameterGroupTranslationEntity
    {
        $result = $this->findOneBy([
            "where" => [
                ["id", "=", $id]
            ]
        ]);

        if (!$result) {
            $message = "Překlad s id '%s' pro skupinu parametrů nebyl nalezen.";
            throw new ProductParameterGroupTranslationNotFoundException(sprintf($message, $id));
        }

        return $result;
    }



    /**
     * @param int $languageId
     * @param string $name
     * @return mixed
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
     * @param int $languageId
     * @return ProductParameterGroupTranslationEntity[]|null
     */
    public function findByLanguageId(int $languageId)
    {
        return $this->findBy([
            "where" => [
                ["languageId", "=", $languageId]
            ], "sort" => [
                ["name"],
                "ASC"
            ]
        ]);
    }



    /**
	 * @param $id int[]
	 * @param $languageId int
	 * @return ProductParameterGroupTranslationEntity[]|array
    */
    public function findByMoreParameterIdAndLanguageId(array $id, int $languageId) : array
	{
		$parameterAnnotation = ProductParameterEntity::getAnnotation();
		$subQuery = sprintf('(SELECT %s FROM %s WHERE %s IN (%s) )',
			$parameterAnnotation->getPropertyByName('productParameterGroupId')->getColumn()->getName(),
			$parameterAnnotation->getTable()->getName(),
			$parameterAnnotation->getPropertyByName('id')->getColumn()->getName(),
			implode(',', $id));

		$filter['sort'] = ['name', 'ASC'];
		$filter['where'][] = ['languageId', '=', $languageId];
		$filter['where'][] = ['productParameterGroupId', 'IN.SQL', $subQuery];
		return $this->findBy($filter) ?: [];
	}
}