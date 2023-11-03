<?php

declare(strict_types = 1);

namespace App\Product\Related;

use App\BaseEntity;
use App\Helpers\Arrays;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_related")
 *
 * @method setProductId($id)
 * @method getProductId()
 * @method setRelatedProductId($id)
 * @method getRelatedProductId()
 * @method getType()
 * @method getParentId()
 */
class Related extends BaseEntity implements IEntity
{


    /** @var string|int types */
    const SET_TYPE = 'set';
    const CROSS_SELLING = 2;
    const SIMILAR = 3;

    /**
     * @Column(name="pr_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="pr_product_id")
     */
    protected $productId;

    /**
     * @Column(name="pr_related_product_id")
     */
    protected $relatedProductId;

    /**
     * @Column(name="pr_type")
     */
    protected $type;

    /**
     * @Column(name="pr_parent_id")
     */
    protected $parentId;

    /** @var array */
    protected static $types = [
        self::SET_TYPE => [
            'key' => self::SET_TYPE,
            'translation' => 'Souprava',
        ], self::CROSS_SELLING => [
            'key' => self::CROSS_SELLING,
            'translation' => 'Cross-selling',
        ], self::SIMILAR => [
        	'key' => self::SIMILAR,
			'translation' => 'Podobný model',
		],
    ];



    /**
     * Set type.
     * @param $type string
     * @return self
     * @throws \InvalidArgumentException unknown type
     */
    public function setType($type) : self
    {
        $types = self::getTypes();
        if (!isset($types[$type])) {
            throw new \InvalidArgumentException(sprintf('Unknown type \'%s\'.', $type));
        }
        $this->type = $type;
        return $this;
    }



    /**
     * @param $pair bool true for return types in pair.
     * @return array
     */
    public static function getTypes(bool $pair = FALSE) : array
    {
        $types = self::$types;
        return $pair === TRUE ? Arrays::toPair($types, 'key', 'translation') : $types;
    }



    /**
     * @param $id int
     * @return self
     * @throws \InvalidArgumentException same id as id of entity
     */
    public function setParentId(int $id) : self
    {
        if ((int)$this->getId() === $id) {
            throw new \InvalidArgumentException('Id can not be same as id of entity.');
        }
        $this->parentId = $id;
        return $this;
    }
}