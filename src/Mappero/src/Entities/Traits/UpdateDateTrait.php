<?php

namespace App;

use Nette\Utils\DateTime;
use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait UpdateDateTrait
{


    /**
     * @param $updateDate string|null
     */
    public function setUpdateDate($updateDate)
    {
        Entities::hasProperty($this, 'updateDate');
        $this->updateDate = $updateDate;
    }



    /**
     * @return string|DateTime|null
     */
    public function getUpdateDate()
    {
        Entities::hasProperty($this, 'updateDate');
        return $this->updateDate;
    }
}