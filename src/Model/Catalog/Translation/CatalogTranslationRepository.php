<?php

declare(strict_types = 1);

namespace App\Catalog\Translation;

use App\IRepository;
use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CatalogTranslationRepository extends BaseRepository implements IRepository
{


    /** @var string */
    protected $entityName = CatalogTranslation::class;



    /**
     * @param int $id
     * @return CatalogTranslation
     * @throws NotFoundException
     */
    public function getOneById(int $id) : CatalogTranslation
    {
        $result = $this->findOneBy([
            'where' => [
                ['id', '=', $id]
            ]
        ]);
        if (!$result) {
            throw new NotFoundException(sprintf('Položka s id "%d" nebyla nalezen.', $id));
        }
        return $result;
    }
}