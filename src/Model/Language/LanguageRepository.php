<?php

namespace App\Language;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class LanguageRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = LanguageEntity::class;



    /**
     * @param $prefix string
     * @return LanguageEntity
     * @throws NotFoundException
     */
    public function getOneByPrefix(string $prefix)
    {
        if ($prefix) {
            $result = $this->findOneBy([
                "where" => [
                    ["prefix", "=", $prefix]
                ]
            ]);
            if ($result) {
                return $result;
            }
        }
        throw new NotFoundException("Jazyk nebyl nalezen.");
    }



    /**
     * @param $id int
     * @return LanguageEntity
     * @throws NotFoundException
     */
    public function getOneById($id)
    {
        if ($id) {
            $result = $this->findOneBy([
                "where" => [
                    ["id", "=", $id]
                ]
            ]);
            if ($result) {
                return $result;
            }
        }
        throw new NotFoundException(sprintf("Jazyk s id '%s' nebyl nalezen.", $id));
    }



    /**
     * @return LanguageEntity[]|array
     */
    public function findActive() : array
    {
        $filter['where'][] = ['active', '=', TRUE];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @return LanguageEntity
    */
    public function getOneDefaultActive() : LanguageEntity
    {
        $filter['where'][] = ['default', '=', TRUE];
        $filter['where'][] = ['active', '=', TRUE];
        return $this->findOneBy($filter) ?: NULL;
    }
}