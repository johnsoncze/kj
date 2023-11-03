<?php

namespace App\ArticleCategory;

use App\AddDateTrait;
use App\Article\Module\Module;
use App\BaseEntity;
use App\LanguageTrait;
use App\SeoTrait;
use Ricaefeliz\Mappero\Entities\IEntity;
use App\EntitySortTrait;
use App\Helpers\IEntitySort;


/**
 * @Table(name="article_category")
 *
 * @method getModule()
 * @method getLanguageId()
 * @method setModuleId($id)
 * @method getModuleId()
 * @method setName($name)
 * @method getName()
 * @method setUrl($url)
 * @method getUrl()
 */
class ArticleCategoryEntity extends BaseEntity implements IEntity, IEntitySort
{


    use AddDateTrait;
    use EntitySortTrait;
    use LanguageTrait;
    use SeoTrait;

    /**
     * @Column(name="ac_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="ac_language_id")
     */
    protected $languageId;

    /**
     * @var Module|null
     */
    protected $module;

    /**
     * @Column(name="ac_module_id")
     */
    protected $moduleId;

    /**
     * @Column(name="ac_name")
     */
    protected $name;

    /**
     * @Column(name="ac_url")
     */
    protected $url;

    /**
     * @Column(name="ac_title_seo")
     */
    protected $titleSeo;

    /**
     * @Column(name="ac_description_seo")
     */
    protected $descriptionSeo;

    /**
     * @Column(name="ac_sort")
     * @var int
     */
    protected $sort;

    /**
     * @Column(name="ac_add_date")
     */
    protected $addDate;



    /**
     * @param $module Module
     * @return self
     */
    public function setModule(Module $module): self
    {
        $this->module = $module;
        return $this;
    }



    /**
     * @return string
     */
    public function getResolvedTitle(): string
    {
        return $this->getTitleSeo() ?: $this->getName();
    }
}