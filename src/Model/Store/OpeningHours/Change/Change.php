<?php

declare(strict_types = 1);

namespace App\Store\OpeningHours\Change;

use App\AddDateTrait;
use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="store_opening_hours_change")
 *
 * @method setDate($date)
 * @method getDate()
 * @method setOpeningTime($time)
 * @method getOpeningTime()
 * @method setClosingTime($time)
 * @method getClosingTime()
 */
class Change extends BaseEntity implements IEntity
{


    use AddDateTrait;

    /**
     * @Column(name="sohc_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="sohc_date")
     */
    protected $date;

    /**
     * @Column(name="sohc_opening_time")
     */
    protected $openingTime;

    /**
     * @Column(name="sohc_closing_time")
     */
    protected $closingTime;

    /**
     * @Column(name="sohc_add_date")
     */
    protected $addDate;
}