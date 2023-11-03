<?php

declare(strict_types = 1);

namespace App\Category;

use App\AddDateTrait;
use App\BaseEntity;
use App\Category\Product\Sorting\Sorter\BasicSorter;
use App\Category\Product\Sorting\Sorter\FromTheCheapest;
use App\Category\Product\Sorting\Sorter\PrioritySorter;
use App\Category\Product\Sorting\Sorter\MauriceLacroixCollectionSorter;
use App\Components\Tree\Sources\EntityParent\IEntityParent;
use App\EntitySortTrait;
use App\FrontModule\Components\SiteMap\ISiteMapItem;
use App\IPublication;
use App\OgTrait;
use App\PublicationTrait;
use App\SeoTrait;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="category")
 *
 * @method setLanguageId($id)
 * @method getLanguageId()
 * @method setParentCategoryId($id)
 * @method getParentCategoryId()
 * @method setParentCategory($category)
 * @method getParentCategory()
 * @method setName($name)
 * @method getName()
 * @method setUrl($url)
 * @method getUrl()
 * @method setContent($content)
 * @method setDescription($description)
 * @method getDescription()
 * @method getContent()
 * @method getRelatedPageText()
 * @method getRelatedPageScrolledText()
 * @method getRelatedPageLink()
 * @method setTemplate($template)
 * @method setImageTemplate($template)
 * @method setShowOnHomepage($arg)
 * @method getShowOnHomepage()
 * @method setMenuImage($image)
 * @method getMenuImage()
 * @method setGeneralImage($image)
 * @method getGeneralImage()
 * @method setGeneralImageDesktop($image)
 * @method getGeneralImageDesktop()
 * @method setGeneralImageMobile($image)
 * @method getGeneralImageMobile()
 * @method setSubcategoryImage($image)
 * @method getSubcategoryImage()
 * @method setCategorySlider($arg)
 * @method getCategorySlider()
 * @method setCategorySliderSort($sort)
 * @method setHomepageSort($sort)
 * @method setTop($top)
 * @method getTop()
 * @method setProductSorter($sorter)
 * @method getProductSorter()
 * @method setDisplayPackageImage($displayPackageImage)
 * @method setRelatedPageText($relatedPageText)
 * @method setRelatedPageScrolledText($relatedPageScrolledText)
 * @method setRelatedPageLink($relatedPageLink)
 * @method setPromoArticleId1($promoArticleId)
 * @method getPromoArticleId1()
 * @method setPromoArticleId2($promoArticleId)
 * @method getPromoArticleId2()
 * @method setPromoArticleId3($promoArticleId)
 * @method getPromoArticleId3()
 * @method setCollectionSubname($collectionSubname)
 * @method getCollectionSubname()
 * @method setCollectionPerex($collectionPerex)
 * @method getCollectionPerex()
 * @method setCollectionText($collectionText)
 * @method getCollectionText()
 * @method setCollectionImage($collectionImage)
 * @method getCollectionImage()
 */
class CategoryEntity extends BaseEntity implements IEntity, IPublication, IEntityParent, ISiteMapItem
{


    /** @var int */
    const MAX_PARENT_DEPTH = 300; //unlimited workaround

    /** @var string */
    const TEMPLATE_DIR = __DIR__ . '/../../../app/FrontModule/presenters/templates/Category/static';
    const IMAGE_TEMPLATE_DIR = self::TEMPLATE_DIR . DIRECTORY_SEPARATOR . '/image';

    use SeoTrait;
    use EntitySortTrait;
    use PublicationTrait;
    use AddDateTrait;
    use OgTrait;

    /**
     * @Column(name="cat_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="cat_language_id")
     */
    protected $languageId;

    /**
     * @Column(name="cat_parent_category_id")
     */
    protected $parentCategoryId;

    /**
     * @var CategoryEntity|null
     * @OneToOne(entity="\App\Category\CategoryEntity")
     */
    protected $parentCategory;

    /**
     * @Column(name="cat_name")
     */
    protected $name;

    /**
     * @Column(name="cat_url")
     */
    protected $url;

    /**
     * @Column(name="cat_content")
     */
    protected $content;

    /**
     * @Column(name="cat_description")
     */
    protected $description;

    /**
     * @Column(name="cat_title_seo")
     */
    protected $titleSeo;

    /**
     * @Column(name="cat_description_seo")
     */
    protected $descriptionSeo;

    /**
     * @Column(name="cat_title_og")
     */
    protected $titleOg;

    /**
     * @Column(name="cat_description_og")
     */
    protected $descriptionOg;

    /**
     * @Column(name="cat_sort")
     */
    protected $sort;

    /**
     * @Column(name="cat_status")
     */
    protected $status;

    /**
     * @Column(name="cat_template")
     */
    protected $template;

    /**
     * @Column(name="cat_image_template")
     */
    protected $imageTemplate;

    /**
	 * todo rename to thumbnailImage
     * @Column(name="cat_menu_image")
     */
    protected $menuImage;

    /**
	 * @Column(name="cat_general_image")
    */
    protected $generalImage;

    /**
	 * @Column(name="cat_general_image_desktop")
    */
    protected $generalImageDesktop;		

    /**
	 * @Column(name="cat_general_image_mobile")
    */
    protected $generalImageMobile;
		
    /**
	 * @Column(name="cat_subcategory_image")
    */
    protected $subcategoryImage;
		
    /**
     * @Column(name="cat_show_on_homepage")
     */
    protected $showOnHomepage;

    /**
     * @Column(name="cat_category_slider")
     */
    protected $categorySlider;

    /**
     * @Column(name="cat_category_slider_sort")
     */
    protected $categorySliderSort;

    /**
     * @Column(name="cat_homepage_sort")
     */
    protected $homepageSort;

    /**
     * @Column(name="cat_top")
     */
    protected $top = 0;

    /**
	 * @var string|null
	 * @Column(name="cat_product_sorter")
    */
    protected $productSorter;

	/**
	 * @var int
	 * @Column(name="cat_display_package_image")
	 */
	protected $displayPackageImage;

    /**
     * @Column(name="cat_related_page_text")
     */
    protected $relatedPageText;

		
    /**
     * @Column(name="cat_related_page_scrolled_text")
     */
    protected $relatedPageScrolledText;

		
    /**
     * @Column(name="cat_related_page_link")
     */
    protected $relatedPageLink;

				
    /**
     * @Column(name="cat_promo_article_id_1")
     */
    protected $promoArticleId1;

		
    /**
     * @Column(name="cat_promo_article_id_2")
     */
    protected $promoArticleId2;


		/**
     * @Column(name="cat_promo_article_id_3")
     */
    protected $promoArticleId3;

		
		/**
     * @Column(name="cat_collection_subname")
     */
    protected $collectionSubname;


		/**
     * @Column(name="cat_collection_perex")
     */
    protected $collectionPerex;

		
		/**
     * @Column(name="cat_collection_text")
     */
    protected $collectionText;

		
		/**
     * @Column(name="cat_collection_image")
     */
    protected $collectionImage;
		
		
    /**
     * @Column(name="cat_add_date")
     */
    protected $addDate;		
		
	/** @var array */
    protected static $productSorters = [
    	1 => [
    		'class' => PrioritySorter::class,
			'description' => 'Priorita + novinka + skladovost',
		],
		2 => [
			'class' => MauriceLacroixCollectionSorter::class,
			'description' => 'Řady hodinek Maurice Lacroix: Novinka + určení + číslo modelu'
		],
		3 => [
			'class' => BasicSorter::class,
			'description' => 'Novinka + skladovost + datum přidání',
		],
        4 => [
            'class' => FromTheCheapest::class,
            'description' => 'Od nejlevnějšího produktu po nejdražší',
        ],
	];


    /**
     * @return int|null
     */
    public function getParentId()
    {
        return $this->parentCategoryId;
    }



    /**
     * @return CategoryEntity|null
     */
    public function getParentEntity()
    {
        return $this->parentCategory;
    }



    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->name;
    }



    /**
     * @return string
     */
    public function getResolvedTitle() : string
    {
        return $this->getTitleSeo() ?: $this->getName();
    }



    /**
     * @param $fullPath bool
     * @return string|null
     */
    public function getTemplate(bool $fullPath = FALSE)
    {
        if ($this->template) {
            return $fullPath === TRUE ? self::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $this->template : $this->template;
        }
        return $this->template;
    }



    /**
     * @param $fullPath bool
     * @return string|null
     */
    public function getImageTemplate(bool $fullPath = FALSE)
    {
        if ($this->imageTemplate) {
            return $fullPath === TRUE ? self::IMAGE_TEMPLATE_DIR . DIRECTORY_SEPARATOR . $this->imageTemplate : $this->imageTemplate;
        }
        return $this->imageTemplate;
    }



    /**
     * @return int
     */
    public function getChildDepth() : int
    {
        return $this->countChildDepth($this->getParentCategory());
    }



    /**
     * @param $actual bool get actual value from property
     * @return int
     */
    public function getSort(bool $actual = FALSE)
    {
    	if ($actual === TRUE) {
    		return $this->sort;
		}
        return $this->sort ?: time();
    }



    /**
     * @inheritdoc
     */
    public function getLocation(LinkGenerator $linkGenerator) : string
    {
        return $linkGenerator->link('Front:Category:default', ['url' => $this->getUrl()]);
    }



    /**
     * @inheritdoc
     */
    public function getChangeFrequency()
    {
        return 'hourly';
    }



    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 1.00;
    }



    /**
     * @param $presenter Presenter
	 * @param $image bool
     * @return string
     * @throws InvalidLinkException
    */
    public function getRelativeLink(Presenter $presenter, bool $image = FALSE) : string
    {
    	$params['url'] = $this->getUrl();
    	$params['productParametersFiltration'] = [];
    	$image === TRUE && $this->getImageTemplate() ? $params['_image'] = 'true' : NULL;
        //máme jeden společný template pro všechny kolekce
    	//if ($image === TRUE) $params['_image'] = 'true';
    	return $presenter->link(':Front:Category:default', $params);
    }



    /**
     * @return mixed
     */
    public function getHomepageSort()
    {
        return $this->homepageSort ?: time();
    }



    /**
     * @return bool
     */
    public function isPublished() : bool
    {
        return $this->getStatus() === IPublication::PUBLISH;
    }



    /**
     * @return mixed
     */
    public function getCategorySliderSort()
    {
        return $this->categorySliderSort ?: time();
    }



    /**
     * Get text breadcrumb navigation without links.
     * @return string
     */
    public function getTextNavigation() : string
    {
        $navigation = '';
        $categories = $this->getCategoryTree();
        foreach ($categories as $category) {
            $navigation .= $category->getName() . '/';
        }
        $navigation = rtrim($navigation, '/');
        return $navigation;
    }



    /**
     * @return bool
     */
    public function isTop() : bool
    {
        return (bool)$this->getTop();
    }



    /**
     * @return CategoryEntity[]|array
     */
    protected function getCategoryTree() : array
    {
        $tree[] = $this; //add self as last item in tree
        $parent = $this->getParentCategory();
        for (; ;) {
            if ($parent instanceof CategoryEntity) {
                $tree[] = $parent;
                $parent = $parent->getParentCategory();
                continue;
            }
            break;
        }
        return array_reverse($tree);
    }



    /**
     * Count own depth in tree.
     * @param $categoryEntity CategoryEntity|null
     * @param $depth int
     * @return int
     */
    protected function countChildDepth(CategoryEntity $categoryEntity = NULL, int $depth = 1) : int
    {
        if ($categoryEntity !== NULL) {
            $depth++;
            if ($categoryEntity->getParentCategory()) {
                return $this->countChildDepth($categoryEntity->getParentCategory(), $depth);
            }
        }
        return $depth;
    }



    /**
     * Get list of page templates.
     * @return array
     */
    public static function getTemplateList() : array
    {
        $array = [];
        $pattern = self::TEMPLATE_DIR . '/*.latte';
        $files = new \GlobIterator($pattern);

        /** @var $file \SplFileInfo */
        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $array[$fileName] = $fileName;
        }
        return $array;
    }



    /**
	 * @return string
	 * @throws \InvalidArgumentException
    */
	public function getUploadFolder() : string
	{
		$id = $this->getId();
		if ($id === NULL) {
			throw new \InvalidArgumentException('Missing id.');
		}
		return 'category/' . $id;
	}



    /**
     * Get list of page templates.
     * @return array
     */
    public static function getImageTemplateList() : array
    {
        $array = [];
        $pattern = self::IMAGE_TEMPLATE_DIR . '/*.latte';
        $files = new \GlobIterator($pattern);

        /** @var $file \SplFileInfo */
        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $array[$fileName] = $fileName;
        }
        return $array;
    }



    /**
	 * @return array
    */
    public static function getProductSorterList() : array
	{
		$list = [];
		$commands = self::$productSorters;
		foreach ($commands as $id => $value) {
			$list[$id] = $value['description'];
		}
		return $list;
	}



	/**
	 * @return array
	*/
	public static function getProductSorters() : array
	{
		return self::$productSorters;
	}



	/**
	 * @return int
	 */
	public function getDisplayPackageImage()
	{
		return $this->displayPackageImage;
	}
}