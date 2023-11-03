<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Payment;

use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartPaymentRepository extends BaseRepository implements IRepository
{


    /** @var string */
    protected $entityName = ShoppingCartPayment::class;



    /**
     * @param int $shoppingCartId
     * @return ShoppingCartPayment
     * @throws ShoppingCartPaymentNotFoundException
     */
    public function getOneByShoppingCartId(int $shoppingCartId) : ShoppingCartPayment
    {
        $payment = $this->findOneBy([
            'where' => [
                ['shoppingCartId', '=', $shoppingCartId]
            ]
        ]);
        if (!$payment) {
            throw new ShoppingCartPaymentNotFoundException('Platba nebyla nalezena.');
        }
        return $payment;
    }
}