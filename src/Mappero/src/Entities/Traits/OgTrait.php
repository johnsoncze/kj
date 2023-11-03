<?php

namespace App;

use Ricaefeliz\Mappero\Helpers\Entities;


trait OgTrait
{

    /**
     * @param $title string|null
     */
    public function setTitleOg($title)
    {
        Entities::hasProperty($this, 'titleOg');
        $this->titleOg = $title;
    }



    /**
     * @return string|null
     */
    public function getTitleOg()
    {
        Entities::hasProperty($this, 'titleOg');
        return $this->titleOg;
    }



    /**
     * @param $description string|null
     */
    public function setDescriptionOg($description)
    {
        Entities::hasProperty($this, 'descriptionOg');
        $this->descriptionOg = $description;
    }



    /**
     * @return string|null
     */
    public function getDescriptionOg()
    {
        Entities::hasProperty($this, 'descriptionOg');
        return $this->descriptionOg;
    }

}