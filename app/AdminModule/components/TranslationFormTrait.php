<?php

namespace App\Components;

use Ricaefeliz\Mappero\Translation\Localization;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait TranslationFormTrait
{


    /** @persistent */
    public $languageId;



    /**
     * @return int|null
     */
    public function getLanguageId()
    {
        return $this->languageId ? (int)$this->languageId : NULL;
    }



    /**
     * @return Localization
     */
    public function getLocale() : Localization
    {
        $locale = new LocalizationResolver();
        if ($id = $this->getLanguageId()) {
            return $locale->getById($id);
        }
        return $locale->getDefault();
    }
}