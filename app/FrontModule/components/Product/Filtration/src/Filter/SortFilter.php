<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Filtration\Filter;

use Kdyby\Translation\ITranslator;
use Nette\Application\Request;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SortFilter extends SelectboxFilter
{


    /** @var string */
    const KEY = 'sort';

    /** @var string values of sort list */
    const SORT_DEFAULT = 'default';
    const SORT_CHEAPEST = 'price_asc';
    const SORT_MOST_EXPENSIVE = 'price_desc';
    const SORT_IN_STOCK = 'in_stock_desc';



    /**
     * @inheritdoc
     */
    public function getType() : string
    {
        return 'sort_filter';
    }



    /**
     * @return array
     */
    public static function getTypes() : array
    {
        return [self::SORT_DEFAULT, self::SORT_CHEAPEST, self::SORT_MOST_EXPENSIVE, self::SORT_IN_STOCK];
    }



    /**
     * @param $translator ITranslator
     * @param $name string
     * @param $default string
     * @return self
     */
    public static function create(ITranslator $translator,
                                  string $name,
                                  string $default) : self
    {
        $default = in_array($default, self::getTypes(), TRUE) ? $default : self::SORT_DEFAULT;
        $sortFilter = new self($translator->translate('category.filter.sort.label'), $name);
        $sortFilter->addItem(new Item($translator->translate('category.filter.sort.item.default.label'), self::SORT_DEFAULT, $default === self::SORT_DEFAULT));
        $sortFilter->addItem(new Item($translator->translate('category.filter.sort.item.cheapest.label'), self::SORT_CHEAPEST, $default === self::SORT_CHEAPEST));
        $sortFilter->addItem(new Item($translator->translate('category.filter.sort.item.mostExpensive.label'), self::SORT_MOST_EXPENSIVE, $default === self::SORT_MOST_EXPENSIVE));
        $sortFilter->addItem(new Item($translator->translate('category.filter.sort.item.inStock.label'), self::SORT_IN_STOCK, $default === self::SORT_IN_STOCK));
        return $sortFilter;
    }



    /**
     * Get value from Http request
     * @param $request Request
     * @return string|null
     */
    public static function getFromHttpRequest(Request $request)
    {
        $parameters = $request->getParameters();
        return isset($parameters[self::KEY]) && in_array($parameters[self::KEY], self::getTypes(), TRUE)
                && $parameters[self::KEY] !== self::SORT_DEFAULT
                ? $parameters[self::KEY]
                : null
        ;
    }
}