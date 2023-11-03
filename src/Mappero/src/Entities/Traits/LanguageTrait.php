<?php

namespace App;

use Ricaefeliz\Mappero\Exceptions\EntityException;
use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait LanguageTrait
{


    /**
     * @param $id int
     * @throws EntityException
     */
    public function setLanguageId(int $id)
    {
        Entities::hasProperty($this, 'languageId');
        if ($this->languageId) {
            throw new EntityException("Language is set. You can not change it. Actual language is '{$id}'.");
        }
        $this->languageId = $id;
    }



    /**
     * @return int|null
     */
    public function getLanguageId()
    {
        Entities::hasProperty($this, 'languageId');
        return $this->languageId;
    }
}