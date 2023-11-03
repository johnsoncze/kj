<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Annotations;

use Ricaefeliz\Mappero\Cache\CacheManager;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class AnnotationManager extends NObject
{


    /** @var CacheManager */
    protected $cacheManager;



    public function __construct()
    {
        $this->cacheManager = new CacheManager();
    }



    /**
     * @return Annotation
     */
    public function getAnnotation(string $entity) : Annotation
    {
        if (!$annotation = $this->cacheManager->load($entity)) {
            $annotationFactory = new AnnotationFactory();
            $annotation = $annotationFactory->create($entity);

            //Check minimal settings
            $annotationCheck = new AnnotationCheck();
            $annotationCheck->checkMinimalSettings($annotation);

            //Save
            $this->cacheManager->save($entity, $annotation);
            AnnotationsSingleton::saveAnnotation($annotation);
        }
        return $annotation;
    }


}