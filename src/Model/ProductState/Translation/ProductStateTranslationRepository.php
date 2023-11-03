<?php

declare(strict_types = 1);

namespace App\ProductState\Translation;

use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductStateTranslationRepository extends BaseRepository
{


    /** @var string */
    protected $entityName = ProductStateTranslation::class;



    /**
     * @param int $languageId
     * @return ProductStateTranslation[]|null
     */
    public function findByLanguageId(int $languageId)
    {
        return $this->findBy([
            'where' => [
                ['languageId', '=', $languageId]
            ]
        ]);
    }
}