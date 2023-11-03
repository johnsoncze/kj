<?php

declare(strict_types = 1);

namespace App\Components\Tree;

use App\Components\Tree\Sources\ISource;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Tree extends Control
{


    /** @var Item[]|array */
    protected $topItems = [];

    /** @var Item[]|array */
    protected $items = [];

    /** @var callable|null */
    protected $customRenderCallback;



    /**
     * @param ISource $source
     * @return Tree
     */
    public function setSource(ISource $source) : self
    {
        $source->apply($this);
        return $this;
    }



    /**
     * @param $id mixed unique identifier of item in whole tree
     * @param $title mixed
     * @param $parentItemId mixed
     * @return Item
     * @throws ItemNotExistsException
     * @throws DuplicateItemException
     */
    public function addItem($id, $title, $parentItemId = NULL) : Item
    {
        if (isset($this->items[$id])) {
            throw new DuplicateItemException("Item with id '$id' exists already.");
        }
        $item = new Item($id, $title);
        if ($parentItemId && !$this->getItem($parentItemId)) {
            throw new ItemNotExistsException("Item with parent id '$parentItemId' not exists.");
        } elseif ($parentItemId) {
            $this->getItem($parentItemId)->addItem($item);
        } else {
            $this->topItems[$id] = $item;
        }
        $this->items[$id] = $item;
        return $item;
    }



    /**
     * @param callable $callback
     * @return Tree
     */
    public function setCustomRenderCallback(callable $callback) :self
    {
        $this->customRenderCallback = $callback;
        return $this;
    }



    /**
     * @return callable|null
     */
    public function getCustomRenderCallback()
    {
        return $this->customRenderCallback;
    }



    /**
     * @param $id
     * @return Item|mixed|null
     */
    public function getItem($id)
    {
        return isset($this->items[$id]) ? $this->items[$id] : NULL;
    }



    /**
     * @return Item[]|array
     */
    public function getItems()
    {
        return $this->items;
    }



    /**
     * @return Item[]|array
     */
    public function getTopItems()
    {
        return $this->topItems;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . "/templates/default.latte");
        $this->template->tree = $this;
        $this->template->items = $this->getTopItems();
        $this->template->render();
    }
}