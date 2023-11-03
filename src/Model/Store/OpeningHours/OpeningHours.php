<?php

declare(strict_types = 1);

namespace App\Store\OpeningHours;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="store_opening_hours")
 *
 * @method setDay($day)
 * @method getDay()
 * @method setOpeningTime($time)
 * @method getOpeningTime()
 * @method setClosingTime($time)
 * @method getClosingTime()
 * @method setSort($sort)
 * @method getSort()
 */
class OpeningHours extends BaseEntity implements IEntity
{


    /**
     * @Column(name="soh_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="soh_day")
     */
    protected $day;

    /**
     * @Column(name="soh_opening_time")
     */
    protected $openingTime;

    /**
     * @Column(name="soh_closing_time")
     */
    protected $closingTime;

    /**
     * @Column(name="soh_sort")
     */
    protected $sort;



    /**
     * @return string
     */
    public function getFormattedHours() : string
    {
        $opening = $this->toFormattedHour(new \DateTime($this->getOpeningTime()));
        $closing = $this->toFormattedHour(new \DateTime($this->getClosingTime()));
        return sprintf('%s-%s', $opening, $closing);
    }



    /**
     * @param $hour \DateTime
     * @return string
     */
    public function toFormattedHour(\DateTime $hour) : string
    {
        return $hour->format('H:i');
    }
}