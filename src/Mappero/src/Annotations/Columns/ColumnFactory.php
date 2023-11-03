<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Annotations;

use App\NObject;
use Nette\Utils\ArrayHash;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ColumnFactory extends NObject
{


    /** @var string annotation name */
    const PRIMARY = "primary";



    /**
     * @param ArrayHash $arrayHash
     * @return Column
     * @throws ColumnFactoryException
     */
    public function create(ArrayHash $arrayHash) : Column
    {
        if (!isset($arrayHash->name)) {
            throw new ColumnFactoryException("Missing column name.");
        }
        $column = new Column($arrayHash->name, $arrayHash->type ?? NULL);
        if (isset($arrayHash->key) && $arrayHash->key == self::PRIMARY) {
            $column->setPrimary(TRUE);
        }
        return $column;
    }
}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ColumnFactoryException extends \Exception
{


}