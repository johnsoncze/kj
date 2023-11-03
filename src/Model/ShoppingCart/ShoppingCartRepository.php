<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

use App\IRepository;
use Kdyby\Translation\ITranslator;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartRepository extends BaseRepository implements IRepository
{


    /** @var string */
    protected $entityName = ShoppingCart::class;



    /**
     * @param int $id
     * @param $translator ITranslator
     * @return ShoppingCart
     * @throws ShoppingCartNotFoundException
     */
    public function getOneById(int $id, ITranslator $translator) : ShoppingCart
    {
        $cart = $this->findOneBy([
            'where' => [
                ['id', '=', $id]
            ]
        ]);
        if (!$cart) {
            $key = sprintf('%s.not.found', ShoppingCartTranslation::getFileName());
            throw new ShoppingCartNotFoundException($translator->translate($key));
        }
        return $cart;
    }



    /**
     * @param $customerId int
     * @return ShoppingCart|null
     * @throws ShoppingCartNotFoundException
     */
    public function getOneByCustomerId(int $customerId)
    {
        $filters['where'][] = ['customerId', '=', $customerId];
        $result = $this->findOneBy($filters);
        if (!$result) {
            throw new ShoppingCartNotFoundException('Shopping cart not found.');
        }
        return $result;
    }
}