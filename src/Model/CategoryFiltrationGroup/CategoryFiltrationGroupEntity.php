<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroup;

use App\AddDateTrait;
use App\BaseEntity;
use App\Category\CategoryEntity;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterEntity;
use App\EntitySortTrait;
use App\IPublication;
use App\PublicationTrait;
use App\SeoTrait;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Exceptions\EntityException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="category_filtration_group")
 *
 * @method getCategory()
 * @method getCategoryId()
 * @method setDescription($description)
 * @method getDescription()
 * @method setParameters($parameters)
 * @method getParameters()
 * @method setShowInMenu($arg)
 * @method getShowInMenu()
 * @method setThumbnailImage($image)
 * @method getThumbnailImage()
 */
class CategoryFiltrationGroupEntity extends BaseEntity implements IEntity, IPublication
{


    use AddDateTrait;
    use PublicationTrait;
    use SeoTrait;
    use EntitySortTrait;

    /**
     * @Column(name="cfg_id", key="Primary")
     */
    protected $id;

    /**
     * @var CategoryEntity|null
     */
    protected $category;

    /**
     * @Column(name="cfg_category_id")
     */
    protected $categoryId;

    /**
     * @OneToMany(entity="\App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterEntity")
     * @var CategoryFiltrationGroupParameterEntity[]|array
     */
    protected $parameters;

    /**
     * @Column(name="cfg_description")
     */
    protected $description;

    /**
     * @Column(name="cfg_title_seo")
     */
    protected $titleSeo;

    /**
     * @Column(name="cfg_description_seo")
     */
    protected $descriptionSeo;

    /**
     * @Column(name="cfg_index_seo")
     */
    protected $indexSeo;

    /**
     * @Column(name="cfg_follow_seo")
     */
    protected $followSeo;

    /**
     * @Column(name="cfg_site_map")
     * todo for sitemap is used indexSeo property. maybe I can remove this property
     */
    protected $siteMap;

    /**
     * @Column(name="cfg_status")
     */
    protected $status;

    /**
     * @Column(name="cfg_show_in_menu")
     */
    protected $showInMenu;

	/**
	 * @Column(name="cfg_thumbnail_image")
	 */
	protected $thumbnailImage;

    /**
     * @Column(name="cfg_add_date")
     */
    protected $addDate;

    /**
     * @Column(name="cfg_sort")
     */
    protected $sort;



    /**
     * @param $category CategoryEntity
     * @return self
     */
    public function setCategory(CategoryEntity $category) : self
    {
        $this->category = $category;
        return $this;
    }



    /**
     * @param $id
     * @return CategoryFiltrationGroupEntity
     * @throws EntityException
     */
    public function setCategoryId($id) : self
    {
        $this->categoryId = $id;
        return $this;
    }



    /**
     * Build frontend link.
     * @param $linkGenerator LinkGenerator
     * @return string
     * @throws \InvalidArgumentException
     * @throws InvalidLinkException
     */
    public function getFrontendLink(LinkGenerator $linkGenerator) : string
    {
        if ($this->getCategory() === NULL) {
            throw new \InvalidArgumentException('Missing category object.');
        }

        //build parameters
        $parameters['url'] = $this->getCategory()->getUrl();
        foreach ($this->getParameters() as $parameter) {
            $productParameter = $parameter->getProductParameter();
            $parameters['productParametersFiltration'][$productParameter->getId()] = $productParameter->getTranslation()->getUrl();
        }

        return $linkGenerator->link('Front:Category:default', $parameters);
    }



    /**
     * todo create property "name" and save it to database
    */
    public function getName()
    {
        return $this->getTitleSeo();
    }



    /**
	 * @param $category CategoryEntity
	 * @return string
	 * @throws \InvalidArgumentException missing id
    */
    public function getUploadFolder(CategoryEntity $category) : string
	{
		$id = $this->getId();
		if ($id === NULL) {
			throw new \InvalidArgumentException('Missing id.');
		}
		return $category->getUploadFolder() . DIRECTORY_SEPARATOR . 'group' . DIRECTORY_SEPARATOR . $id;
	}
}