<?php

namespace Ricaefeliz\Mappero\Repositories;

use App\NotFoundException;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;
use App\NObject;
use Ricaefeliz\Mappero\Mappero;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class BaseRepository extends NObject
{


    /** @var string */
    const DEFAULT_CACHE_NAMESPACE = 'Repository';

    /** @var Mappero */
    protected $entityMapper;

    /** @var string */
    protected $entityName;

    /** @var IStorage */
    protected $storage;



    public function __construct(IStorage $storage,
                                Mappero $mappero)
    {
        $this->entityMapper = $mappero;
        $this->storage = $storage;
    }



    /**
     * Get entity mapper
     * @return Mappero
     */
    protected function getEntityMapper()
    {
        return $this->entityMapper;
    }



    /**
     * Save entity
     * @param $entity mixed
     * @return mixed
     */
    public function save($entity)
    {
        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->save($entity);
    }



    /**
     * Find one by filters
     * @param $filters array
     * @return mixed
     */
    public function findOneBy(array $filters)
    {
        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findOneBy($filters);
    }



    /**
     * Find by filters
     * @param $filters array
     * @return mixed
     */
    public function findBy(array $filters)
    {
        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy($filters);
    }



    /**
     * Find all
     * @return mixed
     */
    public function findAll()
    {
        return $this->findBy([]);
    }



    /**
     * Get count of rows
     * @param $filters array
     * @return CountDTO
     */
    public function count($filters)
    {
        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->count($filters);
    }



    /**
     * Remove entity
     * @param $entity mixed
     * @return int
     * @throws RepositoryException
     * @todo nahradit metodou delete
     */
    public function remove($entity)
    {
        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->remove($entity);
    }



    /**
     * @return string
     * @throws RepositoryException
     */
    protected function getEntityName()
    {
        if (!$this->entityName) {
            throw new RepositoryException("Missing entity name for '" . get_called_class() . "'.");
        }
        return $this->entityName;
    }



    /**
     * Get by more id.
     * @param $id array
     * @return array
     * @throws NotFoundException
     */
    public function getByMoreId(array $id) : array
    {
        $filters['where'][] = ['id', '', $id];
        $result = $this->findBy($filters);
        foreach ($id as $k => $_id) {
            if (isset($result[$_id])) {
                unset($id[$k]);
            }
        }
        if ($id) {
            throw new NotFoundException(sprintf('Položky s id \'%s\' nebyly nalezeny.', implode(',', $id)));
        }
        return $result;
    }



    /**
     * @param $namespace string|null
     * @return Cache
     */
    protected function getCache(string $namespace = NULL) : Cache
    {
        static $caches = [];
        $namespace = $namespace ?: self::DEFAULT_CACHE_NAMESPACE;
        if (!isset($caches[$namespace])) {
            $caches[$namespace] = new Cache($this->storage, $namespace);
        }
        return $caches[$namespace];
    }



    /**
     * @param $key string
     * @return string
     */
    protected function createCacheKey(string $key) : string
    {
        $className = str_replace('\\', '.', get_class());
        return sprintf('%s_%s', $key, $className);
    }



    /**
     * @param $key string
     * @param $query callable
     * @param $cacheParams array
     * @return mixed
     */
    protected function runCachedQuery(string $key, callable $query, array $cacheParams = [Cache::EXPIRE => '5 minutes'])
    {
        $cache = $this->getCache();
        $data = $cache->load($key);

        if ($data === NULL) {
            $data = $query($this);
            $cache->save($key, $data, $cacheParams);
        }

        return $data;
    }
}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class RepositoryException extends \Exception
{


}