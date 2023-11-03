<?php

namespace App\Language;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @Table(name="language")
 *
 * @method setPrefix($prefix)
 * @method getPrefix()
 * @method setName($name)
 * @method getName()
 * @method setDefault($default)
 * @method getDefault()
 * @method setActive($active)
 * @method getActive()
 * @method setAddDate($addDate)
 * @method getAddDate()
 */
class LanguageEntity extends BaseEntity implements IEntity
{


    /**
     * @Column(name="lan_id", key="Primary")
     * @var int
     */
    protected $id;

    /**
     * @Column(name="lan_prefix")
     * @var string
     */
    protected $prefix;

    /**
     * @Column(name="lan_name")
     * @var string
     */
    protected $name;

    /**
     * @Column(name="lan_default")
     * @var bool|int
     */
    protected $default;

    /**
     * @Column(name="lan_active")
     * @var bool|int
     */
    protected $active;

    /**
     * @Column(name="lan_add_date")
     * @var string
     */
    protected $addDate;
}