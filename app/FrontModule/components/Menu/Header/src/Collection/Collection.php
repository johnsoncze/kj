<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Menu\Header\Collection;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Collection
{


    /** @var Node[]|array */
    protected $topItems = [];

    /** @var Node[]|array */
    protected $items = [];



    /**
     * @param $nodes Node[]
     */
    public function __construct(array $nodes)
    {
        foreach ($nodes as $node) {
            $this->process($node, $nodes);
        }
    }



    /**
     * @return Node[]
     */
    public function getItems() : array
    {
        return $this->topItems;
    }



    /**
     * @param $node Node
     * @param $nodes Node[]
     * @return Node
     */
    protected function process(Node $node, array $nodes) : Node
    {
        $category = $node->getCategory();

        //is parent
        if ($category->getParentCategoryId() === NULL) {
            if ($this->getItem($category->getId()) === NULL) {
                $this->addItem($node);
            }
            return $node;
        }

        //has parent
        $parent = $nodes[$category->getParentCategoryId()] ?? NULL;
        if ($parent !== NULL) {
            $parent = $this->process($nodes[$category->getParentCategoryId()], $nodes);
            if ($this->getItem($category->getId()) === NULL) {
                $this->addItem($node, $parent);
            }
        }

        return $node;
    }



    /**
     * @param $node Node
     * @param $parent Node
     * @return Node
     */
    protected function addItem(Node $node, Node $parent = NULL) : Node
    {
        $category = $node->getCategory();
        $parentCategory = $parent !== NULL ? $parent->getCategory() : NULL;

        if (isset($this->items[$category->getId()]) === FALSE) {
            if ($parentCategory === NULL) {
                $this->topItems[$category->getId()] = $node;
            } elseif ($this->getItem($parentCategory->getId()) !== NULL) {
                $this->getItem($parentCategory->getId())->addItem($node);
            }
            $this->items[$category->getId()] = $node;
        }

        return $node;
    }



    /**
     * @param $id int
     * @return Node|null
     */
    protected function getItem(int $id)
    {
        return $this->items[$id] ?? NULL;
    }
}