<?php

declare(strict_types = 1);

namespace App\Product\Translation;

use App\IRepository;
use App\Url\IUrlRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductTranslationRepository extends BaseRepository implements IRepository, IUrlRepository
{


    /** @var string */
    protected $entityName = ProductTranslation::class;



    /**
     * @param int $id
     * @return ProductTranslation
     * @throws ProductTranslationNotFoundException
     */
    public function getOneById(int $id) : ProductTranslation
    {
        $result = $this->findOneBy([
            'where' => [
                ['id', '=', $id]
            ]
        ]);
        if (!$result) {
            throw new ProductTranslationNotFoundException(sprintf('Překlad produktu s id "%d" nebyl nalezen.', $id));
        }
        return $result;
    }



    /**
     * @param $url string
     * @param $languageId int
     * @return ProductTranslation|null
     */
    public function findOneByUrlAndLanguageId(string $url, int $languageId)
    {
        return $this->findOneBy([
            'where' => [
                ['url', '=', $url],
                ['languageId', '=', $languageId]
            ]
        ]) ?: NULL;
    }



    /**
     * @param $productId int
     * @return ProductTranslation[]|array
    */
    public function findByProductId(int $productId) : array
    {
        $filter['where'][] = ['productId', '=', $productId];
        return $this->findBy($filter) ?: [];
    }
}