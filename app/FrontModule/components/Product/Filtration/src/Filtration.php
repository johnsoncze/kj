<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\Filtration;

use App\FrontModule\Components\Category\Filtration\Filter\IFilter;
use App\FrontModule\Components\Product\Filtration\Filter\FilterCollection;
use Nette\Application\UI\Control;
use Nette\Application\UI\InvalidLinkException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Filtration extends Control
{


	/** @var string */
	const FILTER_BLOCK_ID = 'filter-block';

    /** @var IFilter[]|array */
    private $filters = [];

    /** @var bool Is any filter used? */
    private $used = FALSE;

    /** @var string|null */
    private $cancelLink;



    /**
     * @param $filter IFilter
     * @return self
     * todo set FilterCollection object instead of each filter separate
     */
    public function addFilter(IFilter $filter) : self
    {
        if ($filter->isFiltered() === TRUE) {
            $this->used = TRUE;
        }
        $this->filters[] = $filter;
        return $this;
    }



    /**
     * @param $link string
     * @return self
     */
    public function setCancelLink(string $link) : self
    {
        $this->cancelLink = $link;
        return $this;
    }



    public function render()
    {
        $this->template->cancelLink = $this->cancelLink;
        $this->template->filterCollection = $this->createCollection();
        $this->template->used = $this->used;
        $this->template->setFile(__DIR__ . '/templates/default.latte');
        $this->template->render();
    }



    /**
     * Build link for add/remove parameter to url.
     * @param $key mixed
     * @param $value mixed
     * @param $productParameter bool
     * @return string
     * @throws InvalidLinkException
     */
    public function buildLink($key, $value = NULL, bool $productParameter = FALSE) : string
    {
        $presenter = $this->getPresenter();
        $request = $presenter->getRequest();
        $parameters = $request->getParameters();
        $parameters['productParametersFiltration'] = $presenter->productParametersFiltration;
        $keys = is_array($key) ? $key : [$key];
        foreach ($keys as $k) {
            $productParameter === TRUE ? $parameters['productParametersFiltration'][$k] = $value : $parameters[$k] = $value;
        }
        $parameters['pagination'] = NULL; //remove pagination from url
        return $presenter->link('this#' . self::FILTER_BLOCK_ID, $parameters);
    }



    /**
     * @return FilterCollection
     */
    private function createCollection() : FilterCollection
    {
        $collection = new FilterCollection();
        foreach ($this->filters as $filter) {
            $collection->add($filter);
        }
        return $collection;
    }
}