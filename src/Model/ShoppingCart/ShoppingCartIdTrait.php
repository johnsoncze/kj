<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

use Ricaefeliz\Mappero\Exceptions\EntityException;
use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ShoppingCartIdTrait
{


    /**
     * @param $id
     */
    public function setShoppingCartId($id)
    {
        $propertyName = $this->getPropertyName();
        Entities::hasProperty($this, $propertyName);
        $this->{$propertyName} = $id;
    }



    /**
     * @return string|int|null
     */
    public function getShoppingCartId()
    {
        $propertyName = $this->getPropertyName();
        Entities::hasProperty($this, $propertyName);
        return $this->{$propertyName};
    }



    /**
     * @return string
     */
    private function getPropertyName() : string
    {
        return 'shoppingCartId';
    }
}