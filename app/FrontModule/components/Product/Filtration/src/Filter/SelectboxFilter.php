<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Filtration\Filter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SelectboxFilter extends AbstractFilter implements IFilter
{


    /** @var Item[]|array */
    protected $items = [];



    /**
     * @param $item Item
     * @return self
     */
    public function addItem(Item $item) : self
    {
        $this->items[] = $item;
        return $this;
    }



    /**
     * @return Item[]|array
     */
    public function getItems() : array
    {
        return $this->items;
    }



    /**
     * @inheritdoc
     */
    public function getType() : string
    {
        return 'selectbox_filter';
    }



    /**
     * @inheritdoc
     */
    public function isFiltered() : bool
    {
        return FALSE;
    }



    /**
     * @return Item|null
    */
    public function getChecked()
    {
        $items = $this->getItems();
        foreach ($items as $item) {
            if ($item->isChecked()) {
                return $item;
            }
        }
        return NULL;
    }
}