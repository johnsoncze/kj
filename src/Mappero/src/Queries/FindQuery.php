<?php

namespace Ricaefeliz\Mappero\Queries;

use Nette\Database\Table\IRow;
use Ricaefeliz\Mappero\Annotations\Annotation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class FindQuery extends BaseQuery implements IQuery
{


    /**
     * @param $annotation Annotation
     * @param $filters array|null
     * @return null|IRow
     */
    public function findOneBy(Annotation $annotation, array $filters = NULL)
    {
        $result = $this->database->findOneBy($annotation, $filters);
        return $result;
    }



    /**
     * @param $annotation Annotation
     * @param $filters array|null
     * @return null|IRow
     */
    public function findBy(Annotation $annotation, array $filters = NULL)
    {
        $result = $this->database->findBy($annotation, $filters);
        return $result;
    }

}