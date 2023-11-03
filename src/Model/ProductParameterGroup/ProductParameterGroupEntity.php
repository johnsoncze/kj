<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup;

use App\AddDateTrait;
use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslatable;
use Ricaefeliz\Mappero\Translation\TranslationTrait;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_parameter_group")
 *
 * @method setFiltrationType($type)
 * @method getFiltrationType()
 * @method setVariantType($type)
 * @method getVariantType()
 * @method setVisibleOnProductDetail($bool)
 * @method getVisibleOnProductDetail()
 * @method setVisibleInOrder($bool)
 * @method getVisibleInOrder()
 * @method setSort($sort)
 * @method getSort()
 */
class ProductParameterGroupEntity extends BaseEntity implements IEntity, ITranslatable
{


    /** @var string */
    const FILTRATION_TYPE_LIST = 'list';
    const FILTRATION_TYPE_COLOR_LIST = 'color_list';

    const VARIANT_TYPE_IMAGE = 'image';
    const VARIANT_TYPE_SELECTBOX = 'selectbox';

    use AddDateTrait;
    use TranslationTrait;

    /**
     * @Column(name="ppg_id", key="Primary")
     */
    protected $id;

    /**
     * @Translation
     * @OneToMany(entity="\App\ProductParameterGroup\ProductParameterGroupTranslationEntity")
     */
    protected $translations;

    /**
     * @Column(name="ppg_filtration_type")
     */
    protected $filtrationType;

    /**
     * @Column(name="ppg_variant_type")
     */
    protected $variantType;

    /**
	 * @Column(name="ppg_visible_on_product_detail")
    */
    protected $visibleOnProductDetail;

    /**
     * @Column(name="ppg_visible_in_order")
     */
    protected $visibleInOrder;

    /**
	 * @Column(name="ppg_sort")
    */
    protected $sort;

    /**
     * @Column(name="ppg_add_date")
     */
    protected $addDate;


    /** @var array */
    protected static $filtrationTypes = [
        self::FILTRATION_TYPE_LIST => [
            'key' => self::FILTRATION_TYPE_LIST,
            'translation' => 'Seznam',
        ],
        self::FILTRATION_TYPE_COLOR_LIST => [
            'key' => self::FILTRATION_TYPE_COLOR_LIST,
            'translation' => 'Seznam barev',
        ],
    ];

    /** @var array */
    protected static $variantTypes = [
        self::VARIANT_TYPE_IMAGE => [
            'key' => self::VARIANT_TYPE_IMAGE,
            'translation' => 'Obrázek',
        ], self::VARIANT_TYPE_SELECTBOX => [
            'key' => self::VARIANT_TYPE_SELECTBOX,
            'translation' => 'Výběr ze seznamu',
        ],
    ];



    /**
	 * todo renamed because "seen" instead of "visible" is right
     * @return bool
     */
    public function isVisibleInOrder() : bool
    {
        return (bool)$this->getVisibleInOrder();
    }



    /**
	 * @return bool
    */
    public function isSeenOnProductDetail() : bool
	{
		return (bool)$this->getVisibleOnProductDetail();
	}



    /**
     * @return array
     */
    public static function getVariantTypes() : array
    {
        return self::$variantTypes;
    }



    /**
     * @return array
     */
    public static function getFiltrationTypes() : array
    {
        return self::$filtrationTypes;
    }
}