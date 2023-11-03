<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Annotations;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class AnnotationCheck extends NObject
{


    /**
     * @param Annotation $annotation
     * @return Annotation
     * @throws AnnotationCheckException
     */
    public function checkMinimalSettings(Annotation $annotation) : Annotation
    {
        if (!$annotation->getTable() instanceof Table) {
            throw new AnnotationCheckException(sprintf("You must set @%s annotation into class '%s'.", Table::TABLE, $annotation->getEntityName()));
        }
        return $annotation;
    }
}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class AnnotationCheckException extends \Exception
{


}