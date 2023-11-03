<?php

namespace Ricaefeliz\Mappero\Mapping\Translation;

use Ricaefeliz\Mappero\Translation\ITranslatable;
use Ricaefeliz\Mappero\Translation\ITranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ITranslationMapping
{


    /**
     * @param ITranslatable $translatableEntity
     * @param ITranslation $translationEntity
     * @return ITranslatable
     */
    public function map(ITranslatable $translatableEntity, ITranslation $translationEntity) : ITranslatable;
}