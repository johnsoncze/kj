<?php

declare(strict_types = 1);

namespace App\Catalog;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CatalogRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = Catalog::class;



    /**
     * @param int $id
     * @return Catalog
     * @throws NotFoundException
     */
    public function getOneById(int $id) : Catalog
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



    /**
     * @param $id
     * @return Catalog
     * @throws NotFoundException
     */
    public function getOnePublishedById(int $id) : Catalog
    {
        $filter['where'][] = ['id', '=', $id];
        $filter['where'][] = ['state', '=', Catalog::PUBLISH];
        $result = $this->findOneBy($filter);
        if (!$result) {
            throw new NotFoundException(sprintf('Položka s id \'%d\' nebyla nalezena.', $id));
        }
        return $result;
    }



    /**
     * @param $type string
     * @return Catalog[]|array
     */
    public function findByType(string $type) : array
    {
        $filter['where'][] = ['type', '=', $type];
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $type string
     * @return Catalog[]|array
     */
    public function findPublishedByType(string $type) : array
    {
        $filter['where'][] = ['state', '=', Catalog::PUBLISH];
        $filter['where'][] = ['type', '=', $type];
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $id array
     * @return Catalog[]|array
     */
    public function findByMoreId(array $id) : array
    {
        $filter['where'][] = ['id', '', $id];
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }
}