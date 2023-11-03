<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product;

use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartProductDeleteFacade
{


    /** @var ShoppingCartProductRepositoryFactory */
    protected $shoppingCartProductRepoFactory;

    /** @var ITranslator */
    protected $translator;



    /**
     * ShoppingCartProductDeleteFacade constructor.
     * @param ShoppingCartProductRepositoryFactory $shoppingCartProductRepoFactory
     * @param $translator ITranslator
     */
    public function __construct(ShoppingCartProductRepositoryFactory $shoppingCartProductRepoFactory,
                                ITranslator $translator)
    {
        $this->shoppingCartProductRepoFactory = $shoppingCartProductRepoFactory;
        $this->translator = $translator;
    }



    /**
     * @param int $shoppingCartId
     * @param string $hash
     * @return bool
     * @throws ShoppingCartProductDeleteFacadeException
     */
    public function delete(int $shoppingCartId, string $hash) : bool
    {
        try {
            $productRepo = $this->shoppingCartProductRepoFactory->create();
            $product = $productRepo->getOneByShoppingCartIdAndHash($shoppingCartId, $hash, $this->translator);
            $productRepo->remove($product);
            return TRUE;
        } catch (ShoppingCartProductNotFoundException $exception) {
            throw new ShoppingCartProductDeleteFacadeException($exception->getMessage());
        }
    }
}