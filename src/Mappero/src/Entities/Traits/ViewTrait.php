<?php

namespace Ricaefeliz\Mappero\Entities\Traits;

use Ricaefeliz\Mappero\Exceptions\EntityException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ViewTrait
{


    /**
     * @param $id
     * @throws EntityException
     */
    public function setId($id)
    {
        throw new EntityException("This object is representing view. You can not set id.");
    }



    /**
     * @throws EntityException
     */
    public function getId()
    {
        throw new EntityException("This object is representing view. You can not get id.");
    }
}