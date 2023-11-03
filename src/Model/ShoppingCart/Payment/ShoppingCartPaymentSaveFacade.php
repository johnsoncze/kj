<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Payment;

use App\Payment\Payment;
use App\Payment\PaymentAllowedRepositoryFactory;
use App\Payment\PaymentNotFoundException;
use App\ShoppingCart\ShoppingCart;
use App\ShoppingCart\ShoppingCartNotFoundException;
use App\ShoppingCart\ShoppingCartRepositoryFactory;
use App\ShoppingCart\ShoppingCartTranslation;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ShoppingCartPaymentSaveFacade
{


    /** @var ShoppingCartRepositoryFactory */
    protected $shoppingCartRepoFactory;

    /** @var ShoppingCartPaymentFactory */
    protected $shoppingCartPaymentFactory;

    /** @var ShoppingCartPaymentRepositoryFactory */
    protected $shoppingCartPaymentRepoFactory;

    /** @var PaymentAllowedRepositoryFactory */
    protected $paymentRepoFactory;

    /** @var ITranslator $translator */
    protected $translator;



    public function __construct(ShoppingCartPaymentFactory $shoppingCartPaymentFactory,
                                ShoppingCartRepositoryFactory $shoppingCartRepoFactory,
                                ShoppingCartPaymentRepositoryFactory $shoppingCartPaymentRepoFactory,
                                PaymentAllowedRepositoryFactory $paymentRepoFactory,
                                ITranslator $translator)
    {
        $this->shoppingCartPaymentFactory = $shoppingCartPaymentFactory;
        $this->shoppingCartRepoFactory = $shoppingCartRepoFactory;
        $this->shoppingCartPaymentRepoFactory = $shoppingCartPaymentRepoFactory;
        $this->paymentRepoFactory = $paymentRepoFactory;
        $this->translator = $translator;
    }



    /**
     * @param int $shoppingCartId
     * @param int $paymentId
     * @return ShoppingCartPayment
     * @throws ShoppingCartPaymentSaveFacadeException
     */
    public function save(int $shoppingCartId, int $paymentId) : ShoppingCartPayment
    {
        $paymentCartRepo = $this->shoppingCartPaymentRepoFactory->create();

        try {
            $shoppingCart = $this->getShoppingCart($shoppingCartId);
            bdump($paymentId);
            $payment = $this->getPayment($paymentId);
            bdump($payment);
        } catch (ShoppingCartNotFoundException $exception) {
            throw new ShoppingCartPaymentSaveFacadeException($this->translator->translate(sprintf('%s.action.failed', ShoppingCartTranslation::getFileName())));
        } catch (PaymentNotFoundException $exception) {
            throw new ShoppingCartPaymentSaveFacadeException($this->translator->translate('shopping-cart.payment.notFound'));
        }

        try {
            $cartPayment = $paymentCartRepo->getOneByShoppingCartId($shoppingCartId);
            if ((int)$cartPayment->getPaymentId() !== $paymentId) {
                $cartPaymentId = $cartPayment->getId();
                $cartPayment = $this->shoppingCartPaymentFactory->create($shoppingCart, $payment);
                $cartPayment->setId($cartPaymentId);
                $cartPayment->setAddDate(new \DateTime());
                $paymentCartRepo->save($cartPayment);
            }
        } catch (ShoppingCartPaymentNotFoundException $exception) {
            //create new
            $cartPayment = $this->shoppingCartPaymentFactory->create($shoppingCart, $payment);
            $cartPayment->setAddDate(new \DateTime());
            $paymentCartRepo->save($cartPayment);
        }

        return $cartPayment;
    }



    /**
     * @param int $id
     * @return ShoppingCart
     * @throws ShoppingCartNotFoundException
     */
    protected function getShoppingCart(int $id) : ShoppingCart
    {
        $cartRepo = $this->shoppingCartRepoFactory->create();
        return $cartRepo->getOneById($id, $this->translator);
    }



    /**
     * @param int $id
     * @return Payment
     * @throws PaymentNotFoundException
     */
    protected function getPayment(int $id) : Payment
    {
        $paymentRepo = $this->paymentRepoFactory->create();
        return $paymentRepo->getOneById($id, $this->translator);
    }

}