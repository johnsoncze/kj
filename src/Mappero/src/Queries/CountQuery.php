<?php

namespace Ricaefeliz\Mappero\Queries;

use Ricaefeliz\Mappero\Annotations\Annotation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CountQuery extends BaseQuery implements IQuery
{


    /**
     * @param $annotation Annotation
     * @param $filters array|null
     * @return int
     */
    public function execute(Annotation $annotation, array $filters = NULL)
    {
        $result = $this->database->count($annotation, $filters);
        return $result;
    }
}