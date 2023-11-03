<?php

namespace App\Tests\Mappero;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslatable;
use Ricaefeliz\Mappero\Translation\TranslationTrait;


/**
 * @Table(name="table_123")
 */
class Entity1 extends BaseEntity implements IEntity, ITranslatable
{


    use TranslationTrait;

    /**
     * @Column(name="p_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="p_name")
     */
    protected $name;

    /**
     * @Translation
     * @OneToMany(entity="App\Tests\Mappero\Entity2")
     */
    protected $entities2;

    /**
     * @OneToOne(entity="App\Tests\Mappero\Entity3")
     */
    protected $entity3;
}