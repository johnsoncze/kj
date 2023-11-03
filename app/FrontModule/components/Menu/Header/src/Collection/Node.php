<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Menu\Header\Collection;

use App\Category\CategoryEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Node
{


    /** @var CategoryEntity */
    protected $category;

    /** @var Node[]|array */
    protected $items = [];



    public function __construct(CategoryEntity $category)
    {
        $this->category = $category;
    }



    /**
     * @param $node Node
     * @return self
     */
    public function addItem(Node $node) : self
    {
        $this->items[] = $node;
        return $this;
    }



    /**
     * @return CategoryEntity
     */
    public function getCategory()
    {
        return $this->category;
    }



    /**
     * @return Node[]|array
     */
    public function getItems() : array
    {
        return $this->items;
    }



    /**
     * @param $categories CategoryEntity[]
     * @return Node[]
     */
    public static function create(array $categories) : array
    {
        //todo maybe move to database when is saving the category or category in tree
        $sorted = [];
        foreach ($categories as $category) {
            $sorting = sprintf('%010d%02d%010d', $category->getChildDepth(), $category->getParentCategoryId() ?: 0, $category->getSort());
            $sorted[$sorting] = $category;
        }
        ksort($sorted);

        $nodes = [];
        foreach ($sorted as $category) {
            $nodes[$category->getId()] = new static($category);
        }
        return $nodes;
    }
}