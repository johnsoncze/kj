<?php

declare(strict_types = 1);

namespace App\Product;

use App\AddDateTrait;
use App\BaseEntity;
use App\Customer\Customer;
use App\EntitySortTrait;
use App\ExternalSystemIdTrait;
use App\Helpers\Prices;
use App\IPublication;
use App\Product\Photo\IPhoto;
use App\PublicationTrait;
use App\StateTrait;
use App\Vat\VatTrait;
use Kdyby\Translation\ITranslator;
use Nette\Utils\Random;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslatable;
use Ricaefeliz\Mappero\Translation\TranslationTrait;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product")
 *
 * @method setType($type)
 * @method setCode($code)
 * @method getCode()
 * @method getExternalSystemId()
 * @method setFeatured($featured)
 * @method getFeatured()
 * @method setPhoto($photo)
 * @method getPhoto()
 * @method setDiscountAllowed($bool)
 * @method getDiscountAllowed()
 * @method setStockState($state)
 * @method getStockState()
 * @method setEmptyStockState($state)
 * @method getEmptyStockState()
 * @method getStock()
 * @method getPrice()
 * @method setNewUntilTo($date)
 * @method getNewUntilTo()
 * @method setLimitedUntilTo($date)
 * @method getLimitedUntilTo()
 * @method setBestsellerUntilTo($date)
 * @method getBestsellerUntilTo()
 * @method setGoodpriceUntilTo($date)
 * @method getGoodpriceUntilTo()
 * @method setRareUntilTo($date)
 * @method getRareUntilTo()
 * @method setCompleted($completed)
 * @method getCompleted()
 * @method setCommentCompleted($comment)
 * @method getCommentCompleted()
 * @method setSaleOnline($sale)
 * @method getSaleOnline()
 * @method setGoogleMerchantCategory($category)
 * @method getGoogleMerchantCategory()
 * @method setGoogleMerchantBrand($brand)
 * @method getGoogleMerchantBrand()
 * @method setGoogleMerchantBrandText($text)
 * @method getGoogleMerchantBrandText()
 * @method setZboziCzCategory($category)
 * @method getZboziCzCategory()
 * @method setHeurekaCategory($category)
 * @method getHeurekaCategory()
 * @method setSort($sort)
 * @method getSort()
 * @method setTmpDiscount($discount)
 * @method getTmpDiscount()
 */
class Product extends BaseEntity implements IEntity, ITranslatable, IPublication, IPhoto
{


    /** @var int maximal value of external system id */
    const MAX_EXTERNAL_SYSTEM_ID = 8388607;
    const PRODUCT_FEED_CACHE_TAG = 'product_feed';

    /** @var string */
    const DEFAULT_TYPE = 'default';
    const WEEDING_RING_PAIR_TYPE = 'weeding_ring_pair';

    /** @var int */
    const WEEDING_RING_PAIR_TYPE_DEFAULT_STATE_ID = 3;
    const WEEDING_RING_PAIR_TYPE_VAT = 21.0;

    use ExternalSystemIdTrait;

    use AddDateTrait;
    use EntitySortTrait;
    use PublicationTrait;
    use StateTrait;
    use TranslationTrait;
    use VatTrait;


    /**
     * @Column(name="p_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="p_type")
     */
    protected $type;

    /**
     * @Column(name="p_code")
     */
    protected $code;

    /**
     * @Column(name="p_external_system_id")
     */
    protected $externalSystemId;

    /**
     * @Column(name="p_featured")
     */
    protected $featured = 0;

    /**
     * @Column(name="p_photo")
     */
    protected $photo;

    /**
     * @Column(name="p_stock_state")
     */
    protected $stockState;

    /**
     * @Column(name="p_empty_stock_state")
     */
    protected $emptyStockState;

    /**
     * @Column(name="p_stock")
     */
    protected $stock;

    /**
     * @Translation
     * @OneToMany(entity="\App\Product\Translation\ProductTranslation")
     */
    protected $translations;

    /**
     * @Column(name="p_price")
     */
    protected $price;

    /**
     * @Column(name="p_vat")
     */
    protected $vat;

    /**
     * @Column(name="p_discount_allowed")
     */
    protected $discountAllowed;

    /**
     * @Column(name="p_state")
     */
    protected $state;

    /**
     * @Column(name="p_is_completed")
     */
    protected $completed;

    /**
     * @Column(name="p_comment_completed")
     */
    protected $commentCompleted;

    /**
     * @var string|null
     * @Column(name="p_new_until_to")
     */
    protected $newUntilTo;

    /**
     * @var string|null
     * @Column(name="p_limited_until_to")
     */
    protected $limitedUntilTo;

    /**
     * @var string|null
     * @Column(name="p_bestseller_until_to")
     */
    protected $bestsellerUntilTo;

    /**
     * @var string|null
     * @Column(name="p_goodprice_until_to")
     */
    protected $goodpriceUntilTo;

    /**
     * @var string|null
     * @Column(name="p_rare_until_to")
     */
    protected $rareUntilTo;		
		
    /**
     * @Column(name="p_sale_online")
     */
    protected $saleOnline;

    /**
     * @Column(name="p_google_merchant_category")
     */
    protected $googleMerchantCategory;

    /**
     * @Column(name="p_google_merchant_brand")
     */
    protected $googleMerchantBrand;

    /**
     * todo remove this property and insert data into googleMerchantBrand property
     * @deprecated remove after finish toto above
     * @Column(name="p_google_merchant_brand_text")
     */
    protected $googleMerchantBrandText;

    /**
     * @Column(name="p_zbozi_cz_category")
     */
    protected $zboziCzCategory;

    /**
     * @Column(name="p_heureka_category")
     */
    protected $heurekaCategory;

    /**
     * @Column(name="p_sort")
     */
    protected $sort;

    /**
     * @Column(name="p_add_date")
     */
    protected $addDate;

    /**
     * @Column(name="tmp_discount")
     */
    protected $tmpDiscount;

    /** @var array */
    protected static $types = [
        self::DEFAULT_TYPE => [
            'key' => self::DEFAULT_TYPE,
            'translation' => 'Standardní',
        ],
        self::WEEDING_RING_PAIR_TYPE => [
            'key' => self::WEEDING_RING_PAIR_TYPE,
            'translation' => 'Pár snubních prstenů',
        ],
    ];


    /**
     * Setter for externalSystemId property
     * @param $id int
     * @return self
     * @throws \EntityInvalidArgumentException wrong format of id
     */
    public function setExternalSystemId(int $id): self
    {
        if ($this->isValidExternalSystemId($id) !== TRUE) {
            throw new \EntityInvalidArgumentException('Externí id musí být větší než 0.');
        }
        $this->externalSystemId = $id;
        return $this;
    }


    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->newUntilTo && (new \DateTime())->format('Y-m-d') <= $this->newUntilTo;
    }
		
    /**
     * @return bool
     */
    public function isLimited(): bool
    {
        return $this->limitedUntilTo && (new \DateTime())->format('Y-m-d') <= $this->limitedUntilTo;
    }

		
    /**
     * @return bool
     */
    public function isBestseller(): bool
    {
        return $this->bestsellerUntilTo && (new \DateTime())->format('Y-m-d') <= $this->bestsellerUntilTo;
    }

    /**
     * @return bool
     */
    public function isGoodprice(): bool
    {
        return $this->goodpriceUntilTo && (new \DateTime())->format('Y-m-d') <= $this->goodpriceUntilTo;
    }

    /**
     * @return bool
     */
    public function isRare(): bool
    {
        return $this->rareUntilTo && (new \DateTime())->format('Y-m-d') <= $this->rareUntilTo;
    }

    /**
     * @return int
     */
    public function getBadgesCount(): int
    {
				$count = 0;
				if ($this->isNew()) {
						$count++;
				}
				if ($this->isLimited()) {
						$count++;
				}
				if ($this->isBestseller()) {
						$count++;
				}
				if ($this->isGoodprice()) {
						$count++;
				}
				if ($this->isRare()) {
						$count++;
				}
				
				return $count;
    }
		
		
    /**
     * Setter for price property.
     * @param $price float
     * @return self
     * @throws \EntityInvalidArgumentException
     */
    public function setPrice(float $price): self
    {
        if (Prices::isValid($price) !== TRUE) {
            throw new \EntityInvalidArgumentException('Cena musí být větší než 0.');
        }
        $this->price = $price;
        return $this;
    }


    /**
     * Get price after discount if product has allowed applying discount.
     * @param $discount float
     * @return float
     */
    public function getPriceAfterDiscount(float $discount): float
    {
        $price = $this->getPrice();
        $isAllowedDiscount = $this->isDiscountAllowed();
        if ($this->getTmpDiscount()) {
            $discount = 25;
            $isAllowedDiscount = true;
        }
        return $isAllowedDiscount ? Prices::subtractPercent($price, $discount) : $price;
    }


    /**
     * @param $discount float
     * @return float
     */
    public function getPriceAfterDiscountWithoutVat(float $discount): float
    {
        return Prices::toBeforePercent($this->getPriceAfterDiscount($discount), (float)$this->getVat());
    }


    /**
     * Set vat.
     * @param $vat float
     * @return self
     * @throws \EntityInvalidArgumentException on unknown vat
     */
    public function setVat(float $vat): self
    {
        if (!$this->isVatValid($vat)) {
            throw new \EntityInvalidArgumentException('Neznámé DPH.');
        }
        $this->vat = $vat;
        return $this;
    }


    /**
     * Setter for stock property.
     * @param $amount int
     * @return self
     * @throws \EntityInvalidArgumentException invalid amount
     */
    public function setStock(int $amount): self
    {
        if ($amount < 0) {
            throw new \EntityInvalidArgumentException('Skladové množství nemůže být menší než 0.');
        }
        $this->stock = $amount;
        return $this;
    }


    /**
     * @return string|float|null
     */
    public function getVat()
    {
        return $this->vat ? number_format($this->vat, 4, '.', '') : NULL;
    }

		
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
		

    /**
     * @return float
     */
    public function getPriceWithoutVat(): float
    {
        return Prices::toBeforePercent($this->getPrice(), (float)$this->getVat());
    }


    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return (bool)$this->completed;
    }


    /**
     * @return bool
     */
    public function canBeSellOnline(): bool
    {
        return (bool)$this->saleOnline;
    }


    /**
     * @return bool
     */
    public function isInStock(): bool
    {
        return (bool)$this->getStock();
    }


    /**
     * Check if product has required quantity.
     * @param $requiredQuantity int
     * @return bool
     */
    public function hasEnoughQuantity(int $requiredQuantity): bool
    {
        return $this->getStock() >= $requiredQuantity;
    }


    /**
     * @param $quantity
     * @param $translator ITranslator
     * @return self
     * @throws \EntityInvalidArgumentException
     */
    public function subtractFromStock(int $quantity, ITranslator $translator = NULL): self
    {
        if ($this->hasEnoughQuantity($quantity) !== TRUE) {
            $message = $translator ? $translator->translate('product.stock.unavailable') : 'Product is not in the required quantity in stock';
            throw new \EntityInvalidArgumentException($message);
        }
        $this->setStock($this->getStock() - $quantity);
        return $this;
    }


    /**
     * @return int
     */
    public function getStockStateByStockQuantity(): int
    {
        $stateId = $this->getStock() > 0 ? $this->getStockState() : $this->getEmptyStockState();
        return (int)$stateId;
    }


    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->getState() === IPublication::PUBLISH;
    }


    /**
     * @return string
     */
    public function getPriceLabel(): string
    {
        $price = $this->getPrice();
        return self::getPriceLabelByPrice((int)$price);
    }

    /**
     * @param int $price
     * @return string
     */
    public static function getPriceLabelByPrice(int $price): string
    {
        if ($price <= 4999) {
            return '0-4999';
        }
        if ($price >= 5000 && $price <= 9999) {
            return '5000-9999';
        }
        if ($price >= 10000 && $price <= 14999) {
            return '10000-14999';
        }
        if ($price >= 15000 && $price <= 24999) {
            return '15000-24999';
        }
        if ($price >= 25000 && $price <= 39000) {
            return '25000-39000';
        }
        return '40000+';
    }


    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type ?: self::DEFAULT_TYPE;
    }



    /**
     * @return bool
     */
    public function isWeedingRingPair() : bool
    {
        return $this->getType() === self::WEEDING_RING_PAIR_TYPE;
    }


    /**
     * @return bool
     */
    public function isGiftVoucher() : bool
    {
        return (bool)preg_match('~^DP[0-9]+$~', $this->getCode());
    }



    /**
     * @return bool
     */
    public function isFeatured() : bool
    {
        return (bool)$this->getFeatured();
    }



    /**
     * @return bool
    */
    public function hasBadge() : bool
    {
        return $this->isNew();
    }
		

    /**
     * @return string
     * @throws \InvalidArgumentException
    */
    public function getUploadFolder() : string
    {
        $id = $this->getId();
        if ($id === NULL) {
            throw new \InvalidArgumentException('Missing product id.');
        }
        return sprintf('products/%s', $id);
    }

    /**
     * @param int $productId
     * @return string
     */
    public static function getUploadFolderByProductId(int $productId): string
    {
        return 'products/' . $productId;
    }


    /**
	 * @param $quantity int
	 * @return float
    */
	public function calculatePriceByQuantity(int $quantity) : float
	{
		return (float)($this->getPrice() * $quantity);
	}



	/**
	 * @param $discount float
	 * @param $quantity int
	 * @return float
	*/
	public function calculatePriceAfterDiscountByQuantity(float $discount, int $quantity) : float
	{
		return (float)($this->getPriceAfterDiscount($discount) * $quantity);
	}



	/**
	 * @param $customer Customer
	 * @return float
	*/
	public function calculatePriceByCustomer(Customer $customer) : float
	{
		$discount = $customer->getBirthdayCoupon() ? (float)$customer::BIRTHDAY_DISCOUNT : (float)$customer::DISCOUNT;
		
		return (float)$this->getPriceAfterDiscount($discount);
	}



	/**
	 * @return bool
	*/
	public function isDiscountAllowed() : bool
	{
		return (bool)$this->getDiscountAllowed();
	}



	/**
	 * @return string
	 */
	public function createPhotoName() : string
	{
		return Random::generate(10, '0-9');
	}



	/**
	 * @return string|null
	*/
	public function getPhotoName()
	{
		return $this->getPhoto();
	}



	/**
     * @return array
     */
    public static function getTypes() : array
    {
        return self::$types;
    }
}