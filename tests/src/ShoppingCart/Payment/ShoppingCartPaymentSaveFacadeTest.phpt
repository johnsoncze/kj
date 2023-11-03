<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Payment;

use App\Payment\Helpers;
use App\Payment\Payment;
use App\Payment\PaymentRepository;
use App\Payment\PaymentRepositoryFactory;
use App\Payment\Translation\PaymentTranslation;
use App\Payment\Translation\PaymentTranslationRepository;
use App\Payment\Translation\PaymentTranslationRepositoryFactory;
use App\ShoppingCart\Payment\ShoppingCartPayment;
use App\ShoppingCart\Payment\ShoppingCartPaymentRepository;
use App\ShoppingCart\Payment\ShoppingCartPaymentRepositoryFactory;
use App\ShoppingCart\Payment\ShoppingCartPaymentSaveFacadeException;
use App\ShoppingCart\Payment\ShoppingCartPaymentSaveFacadeFactory;
use App\ShoppingCart\ShoppingCart;
use App\ShoppingCart\ShoppingCartHash;
use App\ShoppingCart\ShoppingCartRepository;
use App\ShoppingCart\ShoppingCartRepositoryFactory;
use App\ShoppingCart\ShoppingCartTranslation;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartPaymentSaveFacadeTest extends BaseTestCase
{


    /** @var PaymentRepository|null */
    protected $paymentRepo;

    /** @var PaymentTranslationRepository|null */
    protected $paymentTranslationRepo;

    /** @var ShoppingCartRepository|null */
    protected $shoppingCartRepo;

    /** @var ShoppingCartPaymentRepository|null */
    protected $shoppingCartPaymentRepo;

    /** @var Payment|null */
    protected $forbiddenPayment;

    /** @var Payment|null */
    protected $allowedPayment;

    /** @var Payment|null */
    protected $allowedPayment2;

    /** @var ShoppingCart|null */
    protected $shoppingCart;



    protected function setUp()
    {
        parent::setUp();

        //repos
        $paymentRepoFactory = $this->container->getByType(PaymentRepositoryFactory::class);
        $this->paymentRepo = $paymentRepoFactory->create();

        $paymentTranslationRepoFactory = $this->container->getByType(PaymentTranslationRepositoryFactory::class);
        $this->paymentTranslationRepo = $paymentTranslationRepoFactory->create();

        $shoppingCartRepoFactory = $this->container->getByType(ShoppingCartRepositoryFactory::class);
        $this->shoppingCartRepo = $shoppingCartRepoFactory->create();

        $shoppingCartPaymentRepoFactory = $this->container->getByType(ShoppingCartPaymentRepositoryFactory::class);
        $this->shoppingCartPaymentRepo = $shoppingCartPaymentRepoFactory->create();

        //save test payments
        $payment = new Payment();
        $payment->setExternalSystemId(1);
        $payment->setPrice(89.90);
        $payment->setVat(21.00);
        $payment->setState($payment::FORBIDDEN);
        $this->forbiddenPayment = $payment;

        $payment2 = new Payment();
        $payment2->setExternalSystemId(2);
        $payment2->setPrice(139.90);
        $payment2->setVat(21.00);
        $payment2->setState($payment2::ALLOWED);
        $this->allowedPayment = $payment2;

        $payment3 = new Payment();
        $payment3->setExternalSystemId(3);
        $payment3->setPrice(119.50);
        $payment3->setVat(21.00);
        $payment3->setState($payment3::ALLOWED);
        $this->allowedPayment2 = $payment3;

        $this->paymentRepo->save([$this->forbiddenPayment, $this->allowedPayment, $this->allowedPayment2]);

        $paymentTranslation = new PaymentTranslation();
        $paymentTranslation->setLanguageId(1);
        $paymentTranslation->setPaymentId((int)$this->allowedPayment->getId());
        $paymentTranslation->setName('Platba kartou');

        $paymentTranslation2 = new PaymentTranslation();
        $paymentTranslation2->setLanguageId(2);
        $paymentTranslation2->setPaymentId((int)$this->allowedPayment->getId());
        $paymentTranslation2->setName('Credit card');

        $paymentTranslation3 = new PaymentTranslation();
        $paymentTranslation3->setLanguageId(1);
        $paymentTranslation3->setPaymentId((int)$this->allowedPayment2->getId());
        $paymentTranslation3->setName('Dobírka');

        $this->paymentTranslationRepo->save([$paymentTranslation, $paymentTranslation2, $paymentTranslation3]);
        $this->allowedPayment->setTranslations([$paymentTranslation, $paymentTranslation2]);
        $this->allowedPayment2->setTranslations([$paymentTranslation3]);

        //save test shopping cart
        $shoppingCart = new ShoppingCart();
        $shoppingCart->setIpAddress('::1');
        $shoppingCart->setName('Peter Tester');
        $shoppingCart->setEmail('peter@tester.cz');
        $shoppingCart->setBirthdayCoupon(TRUE);
        $shoppingCart->setHash(ShoppingCartHash::generateHash());
        $this->shoppingCartRepo->save($shoppingCart);
        $this->shoppingCart = $shoppingCart;
    }



    public function testSaveNew()
    {
        $shoppingCartId = (int)$this->shoppingCart->getId();
        $paymentId = (int)$this->allowedPayment->getId();

        /** @var $saveFacadeFactory ShoppingCartPaymentSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartPaymentSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $paymentCart = $saveFacade->save($shoppingCartId, $paymentId);
        $paymentCartFromStorage = $this->shoppingCartPaymentRepo->getOneByShoppingCartId($shoppingCartId);

        Assert::type(ShoppingCartPayment::class, $paymentCart);
        Assert::same($shoppingCartId, (int)$paymentCart->getShoppingCartId());
        Assert::same($paymentId, (int)$paymentCart->getPaymentId());
        Assert::same(0.0, (float)$paymentCart->getDiscount());
        Assert::same((float)$this->allowedPayment->getPrice(), (float)$paymentCart->getPrice());
        Assert::same((float)$this->allowedPayment->getVat(), (float)$paymentCart->getVat());

        Assert::type(ShoppingCartPayment::class, $paymentCartFromStorage);
        Assert::same($shoppingCartId, (int)$paymentCartFromStorage->getShoppingCartId());
        Assert::same($paymentId, (int)$paymentCartFromStorage->getPaymentId());
        Assert::same(0.0, (float)$paymentCartFromStorage->getDiscount());
        Assert::same((float)$this->allowedPayment->getPrice(), (float)$paymentCartFromStorage->getPrice());
        Assert::same((float)$this->allowedPayment->getVat(), (float)$paymentCartFromStorage->getVat());
    }



    public function testUpdate()
    {
        $shoppingCartId = (int)$this->shoppingCart->getId();
        $paymentId = (int)$this->allowedPayment->getId();

        /** @var $saveFacadeFactory ShoppingCartPaymentSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartPaymentSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $saveFacade->save($shoppingCartId, (int)$this->allowedPayment2->getId()); //save a new payment
        $paymentCart = $saveFacade->save($shoppingCartId, $paymentId); //update
        $paymentCartFromStorage = $this->shoppingCartPaymentRepo->getOneByShoppingCartId($shoppingCartId);

        Assert::type(ShoppingCartPayment::class, $paymentCart);
        Assert::same($shoppingCartId, (int)$paymentCart->getShoppingCartId());
        Assert::same($paymentId, (int)$paymentCart->getPaymentId());
        Assert::same(0.0, (float)$paymentCart->getDiscount());
        Assert::same((float)$this->allowedPayment->getPrice(), (float)$paymentCart->getPrice());
        Assert::same((float)$this->allowedPayment->getVat(), (float)$paymentCart->getVat());

        Assert::type(ShoppingCartPayment::class, $paymentCartFromStorage);
        Assert::same($shoppingCartId, (int)$paymentCartFromStorage->getShoppingCartId());
        Assert::same($paymentId, (int)$paymentCartFromStorage->getPaymentId());
        Assert::same(0.0, (float)$paymentCartFromStorage->getDiscount());
        Assert::same((float)$this->allowedPayment->getPrice(), (float)$paymentCartFromStorage->getPrice());
        Assert::same((float)$this->allowedPayment->getVat(), (float)$paymentCartFromStorage->getVat());
    }



    public function testSaveForUnknownPayment()
    {
        $saveFacadeFactory = $this->container->getByType(ShoppingCartPaymentSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->save((int)$this->shoppingCart->getId(), (int)$this->allowedPayment2->getId() + 1);
        }, ShoppingCartPaymentSaveFacadeException::class, 'shopping-cart.payment.notFound');
    }



    public function testSaveForForbiddenPayment()
    {
        $saveFacadeFactory = $this->container->getByType(ShoppingCartPaymentSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->save((int)$this->shoppingCart->getId(), (int)$this->forbiddenPayment->getId());
        }, ShoppingCartPaymentSaveFacadeException::class, 'shopping-cart.payment.notFound');
    }



    public function testSaveForUnknownShoppingCart()
    {
        $saveFacadeFactory = $this->container->getByType(ShoppingCartPaymentSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->save((int)$this->shoppingCart->getId() + 1, (int)$this->allowedPayment->getId());
        }, ShoppingCartPaymentSaveFacadeException::class, sprintf('%s.action.failed', ShoppingCartTranslation::getFileName()));
    }



    protected function tearDown()
    {
        parent::tearDown();

        //delete test data
        $this->shoppingCartRepo->remove($this->shoppingCart);
        $this->paymentRepo->remove($this->allowedPayment);
        $this->paymentRepo->remove($this->allowedPayment2);
        $this->paymentRepo->remove($this->forbiddenPayment);
        $this->shoppingCart = NULL;
        $this->allowedPayment = NULL;
        $this->forbiddenPayment = NULL;
    }
}

(new ShoppingCartPaymentSaveFacadeTest())->run();