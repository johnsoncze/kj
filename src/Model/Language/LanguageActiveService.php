<?php

namespace App\Language;

use App\ServiceException;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class LanguageActiveService extends NObject
{


    /**
     * @param $languageEntity
     * @return LanguageEntity
     */
    public function setActive(LanguageEntity $languageEntity)
    {
        $languageEntity->setActive(TRUE);
        return $languageEntity;
    }



    /**
     * @param $languageEntity
     * @return LanguageEntity
     * @throws ServiceException
     */
    public function setDeactive(LanguageEntity $languageEntity)
    {
        if ($languageEntity->getDefault()) {
            throw new ServiceException("Výchozí jazyk nelze deaktivovat.");
        }
        $languageEntity->setActive(FALSE);
        return $languageEntity;
    }
}