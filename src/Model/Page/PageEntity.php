<?php

namespace App\Page;

use App\AddDateTrait;
use App\BaseEntity;
use App\Components\Tree\Sources\EntityParent\IEntityParent;
use App\EntitySortTrait;
use App\FrontModule\Components\SiteMap\ISiteMapItem;
use App\Helpers\Arrays;
use App\IPublication;
use App\LanguageTrait;
use App\OgTrait;
use App\PublicationTrait;
use App\SeoTrait;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="page")
 *
 * @method setParentPageId($id)
 * @method getParentPageId()
 * @method setParentPage($page)
 * @method getParentPage()
 * @method setSubPages($pages)
 * @method setArticleModuleId($id)
 * @method getArticleModuleId()
 * @method setType($type)
 * @method getType()
 * @method setName($name)
 * @method getName()
 * @method setContent($content)
 * @method getContent()
 * @method setTemplate($template)
 * @method getTemplate()
 * @method setUrl($url)
 * @method getUrl()
 * @method setMenuLocation($location)
 * @method getMenuLocation()
 */
class PageEntity extends BaseEntity implements IEntity, IPublication, IEntityParent, ISiteMapItem
{


    /** @var int menu locations */
    const MENU_LOCATION_HEADER = 1;
    const MENU_LOCATION_FOOTER_PURCHASE = 2;
    const MENU_LOCATION_FOOTER_OUR_WEB = 3;

    /** @var int */
    const MAX_PARENT_DEPTH = 3;

    /** @var string */
    const TEMPLATE_DIR = __DIR__ . '/../../../app/FrontModule/presenters/templates/Page/static';

    use LanguageTrait;
    use PublicationTrait;
    use SeoTrait;
    use EntitySortTrait;
    use AddDateTrait;
    use OgTrait;

    /** @var string */
    const TEXT_TYPE = "text";

    /** @var string */
    const ARTICLES_TYPE = "articles";

    /**
     * @Column(name="p_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="p_language_id")
     */
    protected $languageId;

    /**
     * @Column(name="p_parent_page_id")
     */
    protected $parentPageId;

    /**
     * @Column(name="p_article_module_id")
     */
    protected $articleModuleId;

    /**
     * @OneToOne(entity="\App\Page\PageEntity")
     */
    protected $parentPage;

    /**
     * @var PageEntity[]|array
    */
    protected $subPages = [];

    /**
     * @Column(name="p_type")
     */
    protected $type;

    /**
     * @Column(name="p_name")
     */
    protected $name;

    /**
     * @Column(name="p_content")
     */
    protected $content;

    /**
     * @Column(name="p_url")
     */
    protected $url;

    /**
     * @Column(name="p_title_seo")
     */
    protected $titleSeo;

    /**
     * @Column(name="p_description_seo")
     */
    protected $descriptionSeo;

    /**
     * @Column(name="p_title_og")
     */
    protected $titleOg;

    /**
     * @Column(name="p_description_og")
     */
    protected $descriptionOg;

    /**
     * @Column(name="p_sort")
     */
    protected $sort;

    /**
     * @Column(name="p_setting")
     * @var array|null json to database
     */
    protected $setting;

    /**
     * @Column(name="p_template")
     */
    protected $template;

    /**
     * @Column(name="p_status")
     */
    protected $status;

    /**
     * @Column(name="p_menu_location")
     */
    protected $menuLocation;

    /**
     * @Column(name="p_add_date")
     */
    protected $addDate;


    /** @var array */
    protected static $types = [
        self::TEXT_TYPE => [
            "key" => self::TEXT_TYPE,
            "translate" => "Textová stránka",
            'frontRoute' => 'Front:Page:detail',
        ], self::ARTICLES_TYPE => [
            "key" => self::ARTICLES_TYPE,
            "translate" => "Výpis článků",
            'frontRoute' => 'Front:Article:list',
        ]
    ];

    /** @var array */
    protected static $menuLocations = [
        self::MENU_LOCATION_HEADER => [
            'key' => self::MENU_LOCATION_HEADER,
            'translation' => 'Hlavička',
        ],
        self::MENU_LOCATION_FOOTER_PURCHASE => [
            'key' => self::MENU_LOCATION_FOOTER_PURCHASE,
            'translation' => 'Patička - Vše o nákupu',
        ],
        self::MENU_LOCATION_FOOTER_OUR_WEB => [
            'key' => self::MENU_LOCATION_FOOTER_OUR_WEB,
            'translation' => 'Patička - Náš web'
        ],
    ];



    /**
     * @param $setting array|null
     * @return self
     */
    public function setSetting($setting)
    {
        if (is_string($setting)) {
            $setting = json_decode($setting);
        }
        $this->setting = $setting ? json_encode($setting) : NULL;
        return $this;
    }



    /**
     * @param $array bool
     * @return array|string|null
     */
    public function getSetting($array = FALSE)
    {
        return $this->setting ? ($array ? (array)json_decode($this->setting) : $this->setting) : NULL;
    }



    /**
     * @return array
     */
    public static function getTypes() : array
    {
        return self::$types;
    }



    /**
     * @return int|null
     */
    public function getParentId()
    {
        return $this->parentPageId;
    }



    /**
     * @return PageEntity|null
     */
    public function getParentEntity()
    {
        return $this->parentPage;
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
        return $this->getTitleSeo() ?: $this->getTitle();
    }



    /**
     * @param $linkGenerator LinkGenerator
     * @return string
     * @throws InvalidLinkException
     */
    public function getFrontendLink(LinkGenerator $linkGenerator) : string
    {
        $frontRoute = self::getTypes()[$this->getType()]['frontRoute'];
        return $linkGenerator->link($frontRoute, ['url' => $this->getUrl()]);
    }



    /**
     * @inheritdoc
     */
    public function getLocation(LinkGenerator $linkGenerator) : string
    {
        return $this->getFrontendLink($linkGenerator);
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
     * @return PageEntity[]|array
    */
    public function getSubPages() : array
    {
        return $this->subPages;
    }



    /**
     * @return string|null
    */
    public function getTemplatePath()
    {
        $template = $this->getTemplate();
        return $template ? self::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $template : NULL;
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
     * @param $pair bool
     * @return array
     */
    public static function getMenuLocationList(bool $pair = TRUE) : array
    {
        $menuLocations = self::$menuLocations;
        return $pair ? Arrays::toPair($menuLocations, 'key', 'translation') : $menuLocations;
    }
}