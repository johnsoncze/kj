<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\Filtration\Filter;

use App\FrontModule\Components\Category\Filtration\Filter\CheckboxFilter;
use Nette\Application\Request;
use Nette\Localization\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class StockFilter extends CheckboxFilter
{


    /** @var string */
    const KEY = 'stock';



    /**
     * Create self.
     * @param $translator ITranslator
     * @param $name string
     * @return self
     */
    public static function create(ITranslator $translator, string $name = self::KEY) : self
    {
        return new static($translator->translate('category.filter.stock.label'), $name);
    }



    /**
     * Get value from http request.
     * @param $request Request
     * @return bool|null
     */
    public static function getFromHttpRequest(Request $request)
    {
        $parameters = $request->getParameters();
        return isset($parameters[self::KEY]) && $parameters[self::KEY] !== '0' ? (bool)$parameters[self::KEY] : NULL;
    }
}