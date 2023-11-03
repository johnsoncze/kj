<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product;

use App\ShoppingCart\ShoppingCartTranslation;
use Kdyby\Translation\ITranslator;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartProductRepository extends BaseRepository
{


    /** @var string */
    protected $entityName = ShoppingCartProduct::class;



    /**
     * @param int $id
     * @param ITranslator $translator
     * @return ShoppingCartProduct
     * @throws ShoppingCartProductNotFoundException
     */
    public function getOneById(int $id, ITranslator $translator) : ShoppingCartProduct
    {
        $product = $this->findOneBy([
            'where' => [
                ['id', '=', $id]
            ]
        ]);
        if (!$product) {
            throw new ShoppingCartProductNotFoundException($translator->translate(sprintf('%s.product.not.found', ShoppingCartTranslation::getFileName()), NULL));
        }
        return $product;
    }



    /**
     * @param int $shoppingCartId
     * @param string $hash
     * @param ITranslator $translator
     * @return ShoppingCartProduct
     * @throws ShoppingCartProductNotFoundException
     */
    public function getOneByShoppingCartIdAndHash(int $shoppingCartId, string $hash, ITranslator $translator) : ShoppingCartProduct
    {
        $product = $this->findOneBy([
            'where' => [
                ['shoppingCartId', '=', $shoppingCartId],
                ['hash', '=', $hash]
            ]
        ]);
        if (!$product) {
            throw new ShoppingCartProductNotFoundException($translator->translate(sprintf('%s.product.not.found', ShoppingCartTranslation::getFileName()), NULL));
        }
        return $product;
    }



    /**
     * @param int $cartId
     * @param int $productId
     * @return ShoppingCartProduct
     * @throws ShoppingCartProductNotFoundException
     */
    public function getOneByCartIdAndProductId(int $cartId, int $productId) : ShoppingCartProduct
    {
        $product = $this->findOneBy([
            'where' => [
                ['shoppingCartId', '=', $cartId],
                ['productId', '=', $productId]
            ]
        ]);
        if (!$product) {
            throw new ShoppingCartProductNotFoundException(sprintf('Produkt pro košík s id "%d" s id produktu "%d" nebyl nalezen.', $cartId, $productId));
        }
        return $product;
    }



    /**
     * @param $cartId int
     * @return ShoppingCartProduct[]|array
     */
    public function findByCartId(int $cartId) : array
    {
        $filters['where'][] = ['shoppingCartId', '=', $cartId];
        return $this->findBy($filters) ?: [];
    }

	public function removeInvalidProducts(int $cartId)
	{
		$filters['where'][] = ['shoppingCartId', '=', $cartId];
		$filters['where'][] = ['product.p_state', '!=', 'publish'];

		$invalidProductsInCart = $this->findBy($filters);
		if($invalidProductsInCart) {
			foreach ($invalidProductsInCart as $invalidProduct) {
				$this->remove($invalidProduct);
			}
		}
	}
}