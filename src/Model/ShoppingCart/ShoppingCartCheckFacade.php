<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

use App\Customer\CustomerNotFoundException;
use App\Customer\CustomerRepository;
use App\ShoppingCart\Product\ShoppingCartProductRepository;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ShoppingCartCheckFacade
{


    //todo ceny produktů - přepočet - kontrola

    /** @var ShoppingCartDiscount */
    private $cartDiscount;

    /** @var ShoppingCartProductRepository */
    private $cartProductRepo;

    /** @var ShoppingCartRepository */
    private $cartRepo;

    /** @var CustomerRepository */
    private $customerRepo;

    /** @var ITranslator */
    private $translator;



    public function __construct(CustomerRepository $customerRepository,
                                ShoppingCartDiscount $shoppingCartDiscount,
                                ShoppingCartProductRepository $shoppingCartProductRepository,
                                ShoppingCartRepository $shoppingCartRepository,
                                ITranslator $translator)
    {
        $this->cartDiscount = $shoppingCartDiscount;
        $this->cartRepo = $shoppingCartRepository;
        $this->cartProductRepo = $shoppingCartProductRepository;
        $this->customerRepo = $customerRepository;
        $this->translator = $translator;
    }



    /**
     * Check if user can applied birthday coupon in case that birthday coupon is set on shopping cart
     * @param $cartId int
     * @return null|string string in case that the user can not applied birthday coupon already
     * @throws ShoppingCartCheckFacadeException
     * todo test
     */
    public function checkBirthdayCoupon(int $cartId)
    {
        try {
            $cart = $this->cartRepo->getOneById($cartId, $this->translator);
            if ($cart->getBirthdayCoupon() === TRUE && $cart->getCustomerId()) {
                $customer = $this->customerRepo->getOneAllowedById((int)$cart->getCustomerId());
                if ($customer->hasBirthdayCoupon() !== TRUE) {
                    $cart->setBirthdayCoupon(FALSE);
                    $this->cartRepo->save($cart);
                    $cartProducts = $this->cartProductRepo->findByCartId($cart->getId());
                    foreach ($cartProducts as $cartProduct) { //remove birthday coupon from products
                        $this->cartDiscount->applyBirthdayCoupon($cart, $cartProduct);
                        $this->cartProductRepo->save($cartProduct);
                    }
                    return $this->translator->translate('shopping-cart.birthdaycoupon.canNotBeAppliedAlready');
                }
            }
            return null;
        } catch (ShoppingCartNotFoundException $exception) {
            throw new ShoppingCartCheckFacadeException($exception->getMessage());
        } catch (CustomerNotFoundException $exception) {
            throw new ShoppingCartCheckFacadeException($exception->getMessage());
        }
    }



    /**
     * Check availability products of cart.
     * @param $cartId int
     * @return array messages
     * @throws ShoppingCartCheckFacadeException
     * todo test
     */
    public function checkProducts(int $cartId) : array
    {
        $messages = [];

        try {
            $cart = $this->cartRepo->getOneById($cartId, $this->translator);
            $cartProducts = $this->cartProductRepo->findByCartId($cart->getId());

            foreach ($cartProducts as $cartProduct) {
                $catalogProduct = $cartProduct->getCatalogProduct();
                if ($catalogProduct === NULL || $catalogProduct->isInStock() !== TRUE) {
                    $messages['error'][] = $this->translator->translate('shopping-cart.product.unavailable', ['name' => $cartProduct->getTranslatedName()]);
                } elseif ($catalogProduct->canBeSellOnline() !== TRUE) {
                    $messages['info'][] = $this->translator->translate('shopping-cart.product.canNotBeSaleOnline', ['name' => $cartProduct->getTranslatedName()]);
                } elseif ((float)$cartProduct->getPrice() !== (float)$catalogProduct->getPrice()) {
                    $cartProduct->setPrice($catalogProduct->getPrice());
                    $this->cartProductRepo->save($catalogProduct);
                    $messages['info'][] = $this->translator->translate('shopping-cart.product.price.recalculation', ['name' => $cartProduct->getPrice()]);
                }
            }

            return $messages;
        } catch (ShoppingCartNotFoundException $exception) {
            throw new ShoppingCartCheckFacadeException($exception->getMessage());
        }
    }
}