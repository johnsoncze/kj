<?php

declare(strict_types = 1);

namespace App\Components\Tree;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Item extends NObject
{


    /** @var int|string */
    protected $id;

    /** @var mixed */
    protected $title;

    /** @var int|string|null */
    protected $parentItemId;

    /** @var Item[]|array */
    protected $items = [];



    public function __construct($id, $title)
    {
        $this->id = $id;
        $this->title = $title;
    }



    /**
     * @param $id mixed
     * @return Item
     */
    public function setParentItemId($id) : self
    {
        $this->parentItemId = $id;
        return $this;
    }



    /**
     * @return int|null|string
     */
    public function getParentItemId()
    {
        return $this->parentItemId;
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * @return bool
     */
    public function hasItem() : bool
    {
        return $this->items ? TRUE : FALSE;
    }



    /**
     * @return Item[]|array
     */
    public function getItems()
    {
        return $this->items;
    }



    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }



    /**
     * @param Item $item
     * @return Item
     */
    public function addItem(Item $item) : self
    {
        if (!isset($this->items[$item->getId()])) {
            $item->setParentItemId($this->getId());
            $this->items[$item->getId()] = $item;
        }
        return $this;
    }
}