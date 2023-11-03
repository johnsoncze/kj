<?php

declare(strict_types = 1);

namespace App\Opportunity\Product\Parameter;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="opportunity_product_parameter")
 *
 * @method setProductId($id)
 * @method getProductId()
 * @method setGroup($group)
 * @method getGroup()
 * @method setParameter($parameter)
 * @method getParameter()
 * @method setParameterGroupId($id)
 * @method getParameterGroupId()
 * @method setParameterId($id)
 * @method getParameterId()
 * @method setName($name)
 * @method getName()
 * @method setValue($value)
 * @method getValue()
 */
class Parameter extends BaseEntity implements IEntity
{


    /**
     * @Column(name="opppp_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="opppp_product_id")
     */
    protected $productId;

    /**
     * @var \App\ProductParameterGroup\ProductParameterGroupEntity|null
     * @OneToOne(entity="\App\ProductParameterGroup\ProductParameterGroupEntity", referencedColumn="id")
     */
    protected $group;

    /**
     * @var \App\ProductParameter\ProductParameterEntity|null
     * @OneToOne(entity="\App\ProductParameter\ProductParameterEntity", referencedColumn="id")
     */
    protected $parameter;

    /**
     * @Column(name="opppp_parameter_group_id")
     */
    protected $parameterGroupId;

    /**
     * @Column(name="opppp_parameter_id")
     */
    protected $parameterId;

    /**
     * @Column(name="opppp_name")
     */
    protected $name;

    /**
     * @Column(name="opppp_value")
     */
    protected $value;



    /**
     * @return string
     */
    public function getTranslatedName() : string
    {
        //todo když nastavený jazyk není ten, ve kterém se objednávalo, tak když existuje překlad, zobrazit název z katalogového produktu
        //todo jinak zobrazit název v čase objednávky
        return $this->getName();
    }



    /**
     * @return string
     */
    public function getTranslatedValue() : string
    {
        //todo když nastavený jazyk není ten, ve kterém se objednávalo, tak když existuje překlad, zobrazit název z katalogového produktu
        //todo jinak zobrazit název v čase objednávky
        return $this->getValue();
    }
}