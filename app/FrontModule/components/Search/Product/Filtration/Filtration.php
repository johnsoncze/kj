<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Search\Product\Filtration;

use App\FrontModule\Components\Category\Filtration\Filter\PriceRange;
use App\FrontModule\Components\Category\Filtration\Filter\SortFilter;
use App\FrontModule\Components\Product\Filtration\Filter\StockFilter;
use App\FrontModule\Components\Product\Filtration\FiltrationFactory;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Filtration extends Control
{


    /** @var FiltrationFactory */
    private $filtrationFactory;

    /** @var Translator */
    private $translator;

    /** @var float */
    private $priceFilterMin = 0.0;

    /** @var float */
    private $priceFilterMax = 0.0;



    public function __construct(FiltrationFactory $filtrationFactory,
                                Translator $translator)
    {
        parent::__construct();
        $this->filtrationFactory = $filtrationFactory;
        $this->translator = $translator;
    }



    /**
     * @param $min float
     * @param $max float
     * @return self
     */
    public function setPriceRange(float $min, float $max) : self
    {
        $this->priceFilterMin = $min;
        $this->priceFilterMax = $max;
        return $this;
    }



    /**
     * @return \App\FrontModule\Components\Product\Filtration\Filtration
     */
    public function createComponentFiltration() : \App\FrontModule\Components\Product\Filtration\Filtration
    {
        $presenter = $this->getPresenter();
        $request = $presenter->getRequest();

        //stock filter
        $stockFilter = StockFilter::create($this->translator);
        $stockFilter->setIsChecked((bool)$request->getParameter(StockFilter::KEY) !== FALSE);

        //price range filter
        $priceFilter = new PriceRange($this->translator->translate('category.filter.price.label'), 'price');
        $request->getParameter(PriceRange::PRICE_FROM_KEY) ? $priceFilter->setActualMin((float)$request->getParameter(PriceRange::PRICE_FROM_KEY)) : NULL;
        $request->getParameter(PriceRange::PRICE_TO_KEY) ? $priceFilter->setActualMax((float)$request->getParameter(PriceRange::PRICE_TO_KEY)) : NULL;
        $priceFilter->setMin($this->priceFilterMin);
        $priceFilter->setMax($this->priceFilterMax);

        //sorting
        $sorting = $request->getParameter('sort') ?: SortFilter::SORT_DEFAULT;
        $sortFilter = SortFilter::create($this->translator, SortFilter::KEY, $sorting);

        //create filtration object
        $filtration = $this->filtrationFactory->create();
        $filtration->addFilter($stockFilter);
        $filtration->addFilter($priceFilter);
        $filtration->addFilter($sortFilter);

        return $filtration;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}