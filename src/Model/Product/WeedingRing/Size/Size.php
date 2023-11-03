<?php

declare(strict_types = 1);

namespace App\Product\WeedingRing\Size;

use App\AddDateTrait;
use App\BaseEntity;
use App\Product\WeedingRing\Gender\Gender;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_weeding_ring_size")
 *
 * @method setProductId($id)
 * @method getProductId()
 * @method setSizeId($id)
 * @method getSizeId()
 * @method setGender($gender)
 * @method getGender()
 * @method setPrice($price)
 * @method getPrice()
 * @method setVat($vat)
 * @method getVat()
 */
class Size extends BaseEntity implements IEntity
{


    /** @var string */
    const GENDER_FEMALE = Gender::FEMALE;
    const GENDER_MALE = Gender::MALE;

    use AddDateTrait;

    /**
     * @Column(name="pws_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="pws_product_id")
     */
    protected $productId;

    /**
     * @Column(name="pws_size_id")
     */
    protected $sizeId;

    /**
     * @Column(name="pws_gender")
     */
    protected $gender;

    /**
     * @Column(name="pws_price")
     */
    protected $price;

    /**
     * @Column(name="pws_vat")
     */
    protected $vat;

    /**
     * @Column(name="pws_add_date")
     */
    protected $addDate;
}