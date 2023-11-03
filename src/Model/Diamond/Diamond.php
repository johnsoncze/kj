<?php

declare(strict_types = 1);

namespace App\Diamond;

use App\BaseEntity;
use App\Diamond\Price\Price;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="diamond")
 *
 * @method setType($type)
 * @method getType()
 * @method setSize($size)
 * @method getSize()
 * @method setPrices($prices)
 * @method getPrices()
 * @method setDefaultQualityId($id)
 * @method getDefaultQualityId()
 */
class Diamond extends BaseEntity implements IEntity
{


    /**
     * @Column(name="d_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="d_type")
     */
    protected $type;

    /**
     * @Column(name="d_size")
     */
    protected $size;

    /**
	 * @Column(name="d_default_quality_id")
    */
    protected $defaultQualityId;

    /**
     * @var Price[]|array
     * @OneToMany(entity="\App\Diamond\Price\Price")
     */
    protected $prices = [];



    /**
     * @param $id int
     * @return Price
     * @throws \InvalidArgumentException missing price for quality
     */
    public function getPriceByQualityId(int $id) : Price
    {
        /** @var $prices Price[] */
        $prices = $this->getPrices();
        foreach ($prices as $price) {
            if ($price->isQuality($id) === TRUE) {
                return $price;
            }
        }
        throw new \InvalidArgumentException(sprintf('Missing price with quality id \'%d\'.', $id));
    }

}