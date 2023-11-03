<?php


namespace App;


use Nette\Utils\DateTime;
use Ricaefeliz\Mappero\Annotations\PropertyException;
use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait AddDateTrait
{


    /**
     * @param $addDate string
     * @throws PropertyException
     */
    public function setAddDate($addDate)
    {
        Entities::hasProperty($this, 'addDate');
        $this->addDate = $addDate;
    }



    /**
     * @return string|DateTime|null
     * @throws PropertyException
     */
    public function getAddDate()
    {
        Entities::hasProperty($this, 'addDate');
        return $this->addDate;
    }


}