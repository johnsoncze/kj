<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Filtration\Filter;

use App\Customer\Customer;
use App\Helpers\Prices;
use Nette\Application\Request;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PriceRange extends AbstractFilter implements IFilter
{


    /** @var string */
    const PRICE_FROM_KEY = 'priceFrom';
    const PRICE_TO_KEY = 'priceTo';

    /** @var float|null */
    protected $actualMin;

    /** @var float|null */
    protected $actualMax;

    /** @var float */
    protected $min = 0.0;

    /** @var float */
    protected $max = 0.0;



    /**
     * @param float $actualMin
     * @return self
     */
    public function setActualMin(float $actualMin) : self
    {
        $this->actualMin = $actualMin;
        return $this;
    }



    /**
     * @param float $actualMax
     * @return self
     */
    public function setActualMax(float $actualMax) : self
    {
        $this->actualMax = $actualMax;
        return $this;
    }



    /**
     * @return float
     */
    public function getActualMin() : float
    {
        return $this->actualMin ?: $this->getMin();
    }



    /**
     * @return float
     */
    public function getActualMax() : float
    {
        return $this->actualMax ?: $this->getMax();
    }



    /**
     * @param $price float
     * @return self
     */
    public function setMin(float $price) : self
    {
        $this->min = $price;
        return $this;
    }



    /**
     * @param $max float
     * @return self
     */
    public function setMax(float $max) : self
    {
        $this->max = $max;
        return $this;
    }



    /**
     * @return float
     */
    public function getMin() : float
    {
        return $this->min;
    }



    /**
     * @return float
     */
    public function getMax() : float
    {
        return $this->max;
    }



    /**
     * @return string
     */
    public function getType() : string
    {
        return 'price_range';
    }



    /**
     * @inheritdoc
     */
    public function isFiltered() : bool
    {
        return $this->actualMin || $this->actualMax;
    }



    /**
     * @return string|null
    */
    public function getFormattedActualMin()
    {
        $actualMinPrice = $this->getActualMin();
        return $actualMinPrice ? Prices::toUserFriendlyFormat($actualMinPrice) : NULL;
    }



    /**
     * @return string|null
     */
    public function getFormattedActualMax()
    {
        $actualMaxPrice = $this->getActualMax();
        return $actualMaxPrice ? Prices::toUserFriendlyFormat($actualMaxPrice) : NULL;
    }



    /**
     * Get value from Http request.
     * @param $request Request
     * @param $customer Customer|null
     * @param $priceKey string type of price by constants in this object
     * @return float|null
     */
    public static function getFromHttpRequest(Request $request,
                                              Customer $customer = NULL,
                                              string $priceKey)
    {
        $parameters = $request->getParameters();
        if (isset($parameters[$priceKey])) {
            //is removed customer's discount because filter works with prices after discount
            //but storage query works with price before discount
            //in this case we must remove discount for show products
            //in actual set price
            $discount = $customer instanceof Customer ? Customer::DISCOUNT : 0;
            return Prices::toBeforeDiscount((float)$parameters[$priceKey], $discount);
        }
        return NULL;
    }
}