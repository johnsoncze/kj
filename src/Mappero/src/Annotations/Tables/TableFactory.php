<?php

namespace Ricaefeliz\Mappero\Annotations;

use App\NObject;
use Nette\Utils\ArrayHash;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TableFactory extends NObject
{


    /**
     * @param ArrayHash $arrayHash
     * @return Table
     * @throws TableFactoryException
     */
    public function create(ArrayHash $arrayHash) : Table
    {
        if (!$arrayHash->name) {
            throw new TableFactoryException("Missing table name");
        }
        $type = $arrayHash->type ?? Table::DEFAULT_TYPE;
        return new Table($arrayHash->name, $type);
    }
}


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TableFactoryException extends \Exception
{


}