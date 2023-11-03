<?php

namespace App\PromoArticle;

use App\AddDateTrait;
use App\BaseEntity;
use App\IPublication;
use App\PublicationTrait;
use Nette\Http\FileUpload;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @Table(name="promo_article")
 *
 * @method setTitle($title)
 * @method getTitle()
 * @method setText($text)
 * @method getText()
 * @method getPhoto()
 * @method setUrl($url)
 * @method getUrl()
 * @method setUrlText($urlText)
 * @method getUrlText()
 * @method setIsDefault($isDefault)
 * @method getIsDefault()
 * @method setSequence($sequence)
 * @method getSequence()

 *  * @method setCategories($categories) */
class PromoArticleEntity extends BaseEntity implements IEntity, IPublication
{

	
    /**
     * @Column(name="pa_id", key="Primary")
     * @var int
     */
    protected $id;

		
    /**
     * @Column(name="pa_title")
     * @var string
     */
    protected $title;

		
    /**
     * @Column(name="pa_text")
     * @var string
     */
    protected $text;

		
    /**
     * @Column(name="pa_url")
     * @var string
     */
    protected $url;

    /**
     * @Column(name="pa_url_text")
     * @var string
     */
    protected $urlText;

    /**
     * @Column(name="pa_is_default")
     * @var int
     */
    protected $isDefault;

    /**
     * @Column(name="pa_sequence")
     * @var int
     */
    protected $sequence;

		
    /**
     * @Column(name="pa_photo")
     * @var string|FileUpload
     */
    protected $photo;
		

    /**
     * @param $photo string|FileUpload
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

			/**
		 * @return string
		 * @throws \InvalidArgumentException
			*/
		public function getUploadFolder() : string
		{
			return 'promo-article-photos/';
		}
		
		
}