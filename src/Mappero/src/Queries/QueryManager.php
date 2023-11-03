<?php

namespace Ricaefeliz\Mappero\Managers;

use App\NObject;
use Ricaefeliz\Mappero\Annotations\AnnotationManager;
use Ricaefeliz\Mappero\Annotations\AnnotationsSingleton;
use Ricaefeliz\Mappero\Bridges\NetteDatabase\NetteDatabase;
use Ricaefeliz\Mappero\Entities\EntityFactory;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Exceptions\QueryManagerException;
use Ricaefeliz\Mappero\Helpers\Entities;
use Ricaefeliz\Mappero\Queries\CountQuery;
use Ricaefeliz\Mappero\Queries\FindQuery;
use Ricaefeliz\Mappero\Queries\InsertQuery;
use Ricaefeliz\Mappero\Queries\Queries;
use Ricaefeliz\Mappero\Queries\RemoveQuery;
use Ricaefeliz\Mappero\Queries\UpdateQuery;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class QueryManager extends NObject
{


    /** @var NetteDatabase */
    protected $database;

    /** @var AnnotationManager */
    protected $annotationManager;

    /** @var Queries */
    protected $queries;

    /** @var string */
    protected $entityName;



    public function __construct(NetteDatabase $database, AnnotationManager $annotationManager)
    {
        $this->database = $database;
        $this->annotationManager = $annotationManager;
        $this->queries = new Queries($database);
    }



    /**
     * @param string $name
     * @return $this
     */
    public function setEntityName(string $name)
    {
        $this->entityName = $name;
        return $this;
    }



    /**
     * @param $entity IEntity|IEntity[]
     * @return IEntity|IEntity[]
     * @throws QueryManagerException
     */
    public function save($entity)
    {
        if (!$entity) {
            throw new QueryManagerException("Missing entity for save.");
        }
        if (is_array($entity)) {
            $new = [];
            foreach ($entities = $entity as $entity) {
                if (!$entity->getId()) {
                    $new[] = $entity;
                } else {
                    $updateQuery = $this->queries->getQuery(UpdateQuery::class);
                    $updateQuery->execute($entity);
                }
            }
            if ($new) {
                $insertQuery = $this->queries->getQuery(InsertQuery::class);
                $firstId = $insertQuery->execute($new);
                Entities::setAscendId($new, $firstId);
            }
            return $entity;
        }

        //Update
        if ($entity->getId()) {
            $updateQuery = $this->queries->getQuery(UpdateQuery::class);
            $updateQuery->execute($entity);
        }//Insert a new
        else {
            $insertQuery = $this->queries->getQuery(InsertQuery::class);
            $row = $insertQuery->execute($entity);
            Entities::setId($entity, $row);
        }
        return $entity;
    }



    /**
     * @param array|NULL $filters
     * @param $mappingCallback callable|null
     * @return null|\Ricaefeliz\Mappero\Entities\IEntity[]|mixed
     */
    public function findBy(array $filters = NULL, callable $mappingCallback = NULL)
    {
        $annotation = AnnotationsSingleton::getAnnotation($this->entityName);
        $findQuery = $this->queries->getQuery(FindQuery::class);
        $result = $findQuery->findBy($annotation, $filters);
        if ($result) {
            if ($mappingCallback) {
                return call_user_func($mappingCallback, $result);
            }
            //todo if is set columns, return array object only with required columns
            $entityFactory = new EntityFactory($this->annotationManager);
            return $entityFactory->createMore($annotation, $result);
        }
        return NULL;
    }



    /**
     * @param array|NULL $filters
     * @param $mappingCallback callable|null
     * @return null|IEntity
     */
    public function findOneBy(array $filters = NULL, callable $mappingCallback = NULL)
    {
        $annotation = AnnotationsSingleton::getAnnotation($this->entityName);
        $findQuery = $this->queries->getQuery(FindQuery::class);
        $result = $findQuery->findOneBy($annotation, $filters);
        if ($result) {
            if ($mappingCallback) {
                return call_user_func($mappingCallback, $result);
            }
            $entityFactory = new EntityFactory($this->annotationManager);
            return $entityFactory->create($annotation, $result);
        }
        return NULL;
    }



    /**
     * @param $filters array|null
     * @return CountDTO
     */
    public function count(array $filters = NULL)
    {
        $annotation = AnnotationsSingleton::getAnnotation($this->entityName);
        $countQuery = $this->queries->getQuery(CountQuery::class);
        $result = $countQuery->execute($annotation, $filters);
        return new CountDTO($result);
    }



    /**
     * @param $entity IEntity|IEntity[]
     * @throws QueryManagerException
     */
    public function remove($entity)
    {
        if (!$entity) {
            throw new QueryManagerException("Missing entity for remove.");
        }
        $removeQuery = $this->queries->getQuery(RemoveQuery::class);
        return $removeQuery->execute($entity);
    }
}