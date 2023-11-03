<?php

namespace Ricaefeliz\Mappero\Translation;

use Ricaefeliz\Mappero\Exceptions\TranslationMissingException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ITranslatable
{


    /**
     * @param ITranslation $translation
     * @return self
     */
    public function addTranslation(ITranslation $translation);



    /**
     * @param string|NULL $prefix
     * @return ITranslation
     * @throws TranslationMissingException
     */
    public function getTranslation(string $prefix = NULL) : ITranslation;



    /**
     * @param $translations ITranslation[]
     * @return self
     */
    public function setTranslations(array $translations);



    /**
     * @return ITranslation[]|null
     */
    public function getTranslations();
}