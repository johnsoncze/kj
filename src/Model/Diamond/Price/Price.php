<?php

declare(strict_types = 1);

namespace App\Diamond\Price;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="diamond_price")
 *
 * @method setDiamondId($id)
 * @method getDiamondId()
 * @method setQualityId($quality)
 * @method getQualityId()
 * @method setPrice($price)
 * @method getPrice()
 * @method setVat($vat)
 * @method getVat()
 */
class Price extends BaseEntity implements IEntity
{


    /**
     * @Column(name="dp_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="dp_diamond_id")
     */
    protected $diamondId;

    /**
     * @Column(name="dp_quality_id")
     */
    protected $qualityId;

    /**
     * @Column(name="dp_price")
     */
    protected $price;

    /**
     * @Column(name="dp_vat")
     */
    protected $vat;



    /**
     * @param $id int
     * @return bool
     */
    public function isQuality(int $id) : bool
    {
        return (int)$this->getQualityId() === $id;
    }
}