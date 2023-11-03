<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup\Lock;

use App\AddDateTrait;
use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_parameter_group_lock")
 *
 * @method setGroupId($id)
 * @method getGroupId()
 * @method setKey($key)
 * @method getKey()
 * @method setDescription($description)
 * @method getDescription()
 */
class Lock extends BaseEntity implements IEntity
{


    /** @var string keys */
    const EE_TRACKING_BRAND = 'ee_tracking_brand';
    const GOOGLE_MERCHANT_FEED_BRAND = 'google_merchant_feed_brand';
    const GOOGLE_MERCHANT_FEED_CATEGORY = 'google_merchant_feed_category';
    const GOOGLE_MERCHANT_CUSTOM_LABEL_2 = 'google_merchant_custom_label_2';
    const HEUREKA_CATEGORY = 'heureka_feed_category';
    const ZBOZI_CZ_CATEGORY = 'zbozi_cz_feed_category';
    const DIAMOND_CALCULATOR = 'diamond_calculator';
    const PRODUCT_DETAIL_BENEFIT = 'product_detail_benefit'; //todo merge into PRODUCT_DETAIL
    const PRODUCT_DETAIL_JK_QUALITY = 'product_detail_jk_quality'; //todo merge into PRODUCT_DETAIL
    const PRODUCT_COLLECTION_PREVIEW = 'product_collection_preview';
    const PRODUCT_MAIN_CATEGORY = 'product_main_category';
    const WATCH_GENDER = 'watch_gender';
    const WEEDING_RING_DEMAND_SIZE_PARAMETER_GROUP = 'weeding_ring_demand_size_parameter_group';
    const WEEDING_RING_DEMAND_DIAMOND_QUALITY = 'weeding_ring_demand_diamond_quality';

    use AddDateTrait;

    /**
     * @Column(name="ppgl_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="ppgl_group_id")
     */
    protected $groupId;

    /**
     * @Column(name="ppgl_key")
     */
    protected $key;

    /**
     * @Column(name="ppgl_description")
     */
    protected $description;

    /**
     * @Column(name="ppgl_add_date")
     */
    protected $addDate;
}