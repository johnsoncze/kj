<?php

namespace App\Article;

use App\AddDateTrait;
use App\BaseEntity;
use App\IPublication;
use App\LanguageTrait;
use App\PublicationTrait;
use App\SeoTrait;
use Nette\Http\FileUpload;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="article")
 *
 * @method setName($name)
 * @method getName()
 * @method setUrl($url)
 * @method getUrl()
 * @method getCoverPhoto()
 * @method setIntroduction($Introduction)
 * @method getIntroduction()
 * @method setContent($content)
 * @method getContent()
 * @method getStatus()
 * @method setCategories($categories)
 * @method getCategories()
 */
class ArticleEntity extends BaseEntity implements IEntity, IPublication
{


    use AddDateTrait;
    use LanguageTrait;
    use PublicationTrait;
    use SeoTrait;

    /**
     * @Column(name="art_id", key="Primary")
     * @var int
     */
    protected $id;

    /**
     * @Column(name="art_language_id")
     * @var int
     */
    protected $languageId;

    /**
     * @Column(name="art_name")
     * @var string
     */
    protected $name;

    /**
     * @Column(name="art_url")
     * @var string
     */
    protected $url;

    /**
     * @Column(name="art_title_seo")
     * @var string
     */
    protected $titleSeo;

    /**
     * @Column(name="art_description_seo")
     * @var string
     */
    protected $descriptionSeo;

    /**
     * @Column(name="art_cover_photo")
     * @var string|FileUpload
     */
    protected $coverPhoto;

    /**
     * @Column(name="art_introduction")
     * @var string
     */
    protected $introduction;

    /**
     * @Column(name="art_content")
     * @var string
     */
    protected $content;

    /**
     * @Column(name="art_status")
     * @var string
     */
    protected $status;

    /**
     * @Column(name="art_add_date")
     * @var string
     */
    protected $addDate;

    /**
     * @OneToMany(entity="App\ArticleCategoryRelationship\ArticleCategoryRelationshipEntity")
     */
    protected $categories;



    /**
     * @param $photo string|FileUpload
     */
    public function setCoverPhoto($photo)
    {
        $this->coverPhoto = $photo;
    }



    /**
     * @return string
    */
    public function getResolvedTitle() : string
    {
        return $this->getTitleSeo() ?: $this->getName();
    }
}