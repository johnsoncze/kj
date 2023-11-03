<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Mapping\Translation;

use App\NObject;
use Ricaefeliz\Mappero\Translation\ITranslatable;
use Ricaefeliz\Mappero\Translation\ITranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TranslationMapping extends NObject
{


    /**
     * @param ITranslatable $translatableEntity
     * @param ITranslation $translationEntity
     * @return ITranslatable
     */
    public function map(ITranslatable $translatableEntity, ITranslation $translationEntity) : ITranslatable
    {
        $translatableEntity->addTranslation($translationEntity);
        return $translatableEntity;
    }
}