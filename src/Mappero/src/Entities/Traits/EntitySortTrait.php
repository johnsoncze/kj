<?php

namespace App;

use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait EntitySortTrait
{


    /**
     * @param int $sort
     * @return $this
     */
    public function setSort($sort)
    {
        Entities::hasProperty($this, 'sort');

        //add language id for a new entity or saved entity if is updating of sort in case of language id property exists
        if ($sort !== NULL && !empty($this->languageId) && ($this->getId() === NULL || ($this->getId() !== NULL && $this->getSort(TRUE) !== NULL))) {
            $this->sort = (int)$this->languageId . $sort;
        } else {
            $this->sort = $sort;
        }
        return $this;
    }



    /**
     * @param $actual bool get actual value from property
     * @return int
     */
    public function getSort(bool $actual = FALSE)
    {
        Entities::hasProperty($this, 'sort');

        if ($actual === TRUE) {
            return $this->sort;
        }

        if ($this->sort) {
            $sort = $this->sort;
        } elseif (!$this->sort && !empty($this->languageId)) {
            $sort = (int)$this->languageId . 0;
        } else {
            $sort = time();
        }
        return $sort;
    }


}