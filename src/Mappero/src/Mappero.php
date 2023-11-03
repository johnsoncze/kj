<?php

namespace Ricaefeliz\Mappero;

use App\NObject;
use Ricaefeliz\Mappero\Annotations\AnnotationManager;
use Ricaefeliz\Mappero\Bridges\NetteDatabase\NetteDatabase;
use Ricaefeliz\Mappero\Managers\QueryManager;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Mappero extends NObject
{


    /** @var QueryManager */
    protected $queryManager;



    public function __construct(NetteDatabase $database)
    {
        $annotationManager = new AnnotationManager();
        $this->queryManager = new QueryManager($database, $annotationManager);
    }



    /**
     * @param string $entityName
     * @return QueryManager
     */
    public function getQueryManager(string $entityName) : QueryManager
    {
        $queryManager = $this->queryManager;
        $queryManager->setEntityName($entityName);
        return $queryManager;
    }
}