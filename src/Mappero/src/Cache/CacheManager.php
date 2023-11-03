<?php

namespace Ricaefeliz\Mappero\Cache;

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CacheManager extends NObject
{


    /** @var string */
    const CACHE_KEY = "entities";

    /** @var string */
    public static $path = __DIR__ . "/../../../../temp/cache";

    /** @var FileStorage */
    protected $fileStorage;

    /** @var Cache */
    protected $cache;



    public function __construct()
    {
        if (!is_dir(self::$path)) {
            @mkdir(self::$path);
        }
        $this->fileStorage = new FileStorage(self::$path);
        $this->cache = new Cache($this->fileStorage, self::CACHE_KEY);
    }



    /**
     * @param $entity string
     * @param $data mixed
     * @return mixed
     */
    public function save(string $entity, $data)
    {
        $this->cache->save($entity, $data);
        return $data;
    }



    /**
     * @param $entity string
     * @return mixed
     */
    public function load(string $entity)
    {
        return $this->cache->load($entity);
    }
}