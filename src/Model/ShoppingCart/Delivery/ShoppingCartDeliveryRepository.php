<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Delivery;

use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartDeliveryRepository extends BaseRepository implements IRepository
{


    /** @var string */
    protected $entityName = ShoppingCartDelivery::class;



    /**
     * @param int $shoppingCartId
     * @return ShoppingCartDelivery
     * @throws ShoppingCartDeliveryNotFoundException
     */
    public function getOneByShoppingCartId(int $shoppingCartId) : ShoppingCartDelivery
    {
        $delivery = $this->findOneBy([
            'where' => [
                ['shoppingCartId', '=', $shoppingCartId]
            ]
        ]);
        if (!$delivery) {
            throw new ShoppingCartDeliveryNotFoundException('Doprava nebyla nalezena.');
        }
        return $delivery;
    }
}