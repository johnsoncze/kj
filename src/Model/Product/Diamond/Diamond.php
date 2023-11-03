<?php

declare(strict_types = 1);

namespace App\Product\Diamond;

use App\AddDateTrait;
use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_diamond")
 *
 * @method setProductId($id)
 * @method getProductId()
 * @method setDiamondId($id)
 * @method getDiamondId()
 * @method setGender($gender)
 * @method getGender()
 * @method getQuantity()
 * @method setDiamond($diamond)
 * @method getDiamond()
 */
class Diamond extends BaseEntity implements IEntity
{


    use AddDateTrait;

    /**
     * @Column(name="pd_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="pd_product_id")
     */
    protected $productId;

    /**
     * @Column(name="pd_diamond_id")
     */
    protected $diamondId;

    /**
     * @Column(name="pd_gender")
     */
    protected $gender;

    /**
     * @Column(name="pd_quantity")
     */
    protected $quantity;

    /**
     * @Column(name="pd_add_date")
     */
    protected $addDate;

    /**
     * @var \App\Diamond\Diamond|null
     * @OneToOne(entity="\App\Diamond\Diamond")
     */
    protected $diamond;



    /**
     * @param $quantity int
     * @return self
     * @throws \EntityInvalidArgumentException
     */
    public function setQuantity(int $quantity) : self
    {
        if ($quantity <= 0) {
            throw new \EntityInvalidArgumentException('Množství musí být větší jak 0.');
        }
        $this->quantity = $quantity;
        return $this;
    }
}