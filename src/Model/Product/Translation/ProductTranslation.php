<?php

declare(strict_types = 1);

namespace App\Product\Translation;

use App\AddDateTrait;
use App\BaseEntity;
use App\LanguageTrait;
use App\OgTrait;
use App\Product\Product;
use App\SeoTrait;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_translation")
 *
 * @method setProductId($id)
 * @method getProductId()
 * @method setLanguageId($id)
 * @method getLanguageId()
 * @method setName($name)
 * @method getName()
 * @method setDescription($description)
 * @method getDescription()
 * @method setShortDescription($description)
 * @method getShortDescription()
 * @method setGoogleMerchantTitle($title)
 * @method getGoogleMerchantTitle()
 * @method setUrl($url)
 * @method getUrl()
 */
class ProductTranslation extends BaseEntity implements IEntity, ITranslation
{


    use AddDateTrait;
    use LanguageTrait;
    use SeoTrait;
    use OgTrait;

    /**
     * @Column(name="pt_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="pt_product_id")
     */
    protected $productId;

    /**
     * @Column(name="pt_language_id")
     */
    protected $languageId;

    /**
     * @Column(name="pt_name")
     */
    protected $name;

    /**
     * @Column(name="pt_description")
     */
    protected $description;

    /**
	 * @Column(name="pt_short_description")
    */
    protected $shortDescription;

    /**
     * @Column(name="pt_title_seo")
     */
    protected $titleSeo;

    /**
     * @Column(name="pt_description_seo")
     */
    protected $descriptionSeo;

    /**
     * @Column(name="pt_title_og")
     */
    protected $titleOg;

    /**
     * @Column(name="pt_description_og")
     */
    protected $descriptionOg;

    /**
     * @Column(name="pt_google_merchant_title")
     */
    protected $googleMerchantTitle;

    /**
     * @Column(name="pt_url")
     */
    protected $url;

    /**
     * @Column(name="pt_add_date")
     */
    protected $addDate;



    /**
     * Get full name of product.
     * @param $product Product
     * @return string
     */
    public function getFullName(Product $product) : string
    {
        return $this->getName() . ' ' . $product->getCode();
    }



    /**
     * @param $product Product
     * @return string
     */
    public function getResolvedTitle(Product $product) : string
    {
        return $this->getTitleSeo() ?: $this->getFullName($product);
    }



    /**
     * @param $product Product
     * @return string
     */
    public function getResolvedGoogleMerchantTitle(Product $product) : string
    {
        return $this->getGoogleMerchantTitle() ?: $this->getFullName($product);
    }
}