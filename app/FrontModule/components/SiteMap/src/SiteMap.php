<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\SiteMap;

use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SiteMap extends Control
{


    /** @var ISiteMapItem[]|array */
    private $items = [];

    /** @var string|null */
    private $cacheId;



    /**
     * Add a item.
     * @param $item ISiteMapItem
     * @return self
     */
    public function addItem(ISiteMapItem $item) : self
    {
        $this->items[] = $item;
        return $this;
    }



    /**
     * Set cache id.
     * @param $id string
     * @return self
     */
    public function setCacheId(string $id) : self
    {
        $this->cacheId = $id;
        return $this;
    }



    /**
     * Get cache id.
     * @return string
     */
    public function getCacheId() : string
    {
        return $this->cacheId ?: 'sitemap';
    }



    /**
     * Get sitemap items.
     * @return ISiteMapItem[]|array
     */
    public function getItems() : array
    {
        return $this->items;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}