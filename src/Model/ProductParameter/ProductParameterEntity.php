<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\BaseEntity;
use App\EntitySortTrait;
use App\ProductParameter\Helper\Helper;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslatable;
use Ricaefeliz\Mappero\Translation\TranslationTrait;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_parameter")
 *
 * @method setHelperId($id)
 * @method getHelperId()
 * @method getHelper()
 * @method getGroup()
 * @method getProductParameterGroupId()
 * @method setAddDate($date)
 * @method getAddDate()
 */
class ProductParameterEntity extends BaseEntity implements IEntity, ITranslatable
{


    use EntitySortTrait;
    use TranslationTrait;

    /**
     * @Column(name="pp_id", key="Primary")
     */
    protected $id;

    /**
     * @Translation
     * @OneToMany(entity="\App\ProductParameter\ProductParameterTranslationEntity")
     */
    protected $translations;

	/**
	 * @var Helper|null
	 */
	protected $helper;

    /**
	 * @Column(name="pp_helper_id")
    */
    protected $helperId;

    /** @var ProductParameterGroupEntity|null */
    protected $group;

    /**
     * @Column(name="pp_product_parameter_group_id")
     */
    protected $productParameterGroupId;

    /**
     * @Column(name="pp_sort")
     */
    protected $sort;

    /**
     * @Column(name="pp_add_date")
     */
    protected $addDate;



	/**
     * @param $id
     * @return $this
     * @throws \Exception
     */
    public function setProductParameterGroupId($id)
    {
        if ($this->productParameterGroupId) {
            throw new \Exception(sprintf("Property with id of product parameter group is set. You can not change it."));
        }

        $this->productParameterGroupId = $id;
        return $this;
    }



    /**
     * @param $group ProductParameterGroupEntity
     * @return self
    */
    public function setGroup(ProductParameterGroupEntity $group) : self
    {
        $this->group = $group;
        return $this;
    }



    /**
	 * @param $helper Helper
	 * @return self
    */
    public function setHelper(Helper $helper) : self
	{
		$this->helper = $helper;
		return $this;
	}
}