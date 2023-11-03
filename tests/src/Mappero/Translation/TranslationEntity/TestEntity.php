<?php

namespace Ricaefeliz\Mappero\Mapping\Translation;


use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslatable;
use Ricaefeliz\Mappero\Translation\TranslationTrait;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TestEntity extends BaseEntity implements IEntity, ITranslatable
{


    use TranslationTrait;
}