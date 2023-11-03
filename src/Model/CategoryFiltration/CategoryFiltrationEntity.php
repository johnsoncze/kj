<?php

declare(strict_types = 1);

namespace App\CategoryFiltration;

use App\AddDateTrait;
use App\BaseEntity;
use App\EntitySortTrait;
use App\SeoTrait;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="category_filtration")
 *
 * @method getCategoryId()
 * @method getProductParameterGroupId()
 * @method setProductParameterGroup($entity)
 * @method getProductParameterGroup()
 * @method setSort($sort)
 */
class CategoryFiltrationEntity extends BaseEntity implements IEntity
{


    use SeoTrait;
    use AddDateTrait;
    use EntitySortTrait;

    /**
     * @Column(name="cf_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="cf_category_id")
     */
    protected $categoryId;

    /**
     * @Column(name="cf_product_parameter_group_id")
     */
    protected $productParameterGroupId;

    /**
     * @OneToOne(entity="\App\ProductParameterGroup\ProductParameterGroupEntity")
     */
    protected $productParameterGroup;

    /**
     * @Column(name="cf_index_seo")
     */
    protected $indexSeo;

    /**
     * @Column(name="cf_follow_seo")
     */
    protected $followSeo;

    /**
     * @Column(name="cf_site_map")
     */
    protected $siteMap;

    /**
     * @Column(name="cf_sort")
     */
    protected $sort;

    /**
     * @Column(name="cf_add_date")
     */
    protected $addDate;



    /**
     * @param $id
     * @return CategoryFiltrationEntity
     * @throws CategoryFiltrationEntityException
     */
    public function setCategoryId($id) : self
    {
        if ($this->categoryId) {
            throw new CategoryFiltrationEntityException("You can not change category id.");
        }

        $this->categoryId = $id;
        return $this;
    }



    /**
     * @param $id
     * @return $this
     * @throws CategoryFiltrationEntityException
     */
    public function setProductParameterGroupId($id)
    {
        if ($this->productParameterGroupId) {
            throw new CategoryFiltrationEntityException("You can not change product parameter group id.");
        }

        $this->productParameterGroupId = $id;
        return $this;
    }


}