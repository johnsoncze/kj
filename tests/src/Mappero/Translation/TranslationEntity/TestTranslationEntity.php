<?php

declare(strict_types = 1);

namespace App\Tests\Mappero\Translation;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TestTranslationEntity extends BaseEntity implements IEntity, ITranslation
{


    protected $languageId;



    /**
     * @param int $translationId
     * @return TestTranslationEntity
     */
    public function setLanguageId(int $translationId) : self
    {
        $this->languageId = $translationId;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }


}