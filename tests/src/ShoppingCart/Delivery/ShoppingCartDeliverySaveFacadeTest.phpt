<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Delivery;

use App\Delivery\Delivery;
use App\Delivery\DeliveryRepository;
use App\Delivery\DeliveryRepositoryFactory;
use App\Delivery\Translation\DeliveryTranslation;
use App\Delivery\Translation\DeliveryTranslationRepositoryFactory;
use App\Helpers\Entities;
use App\ShoppingCart\Delivery\ShoppingCartDelivery;
use App\ShoppingCart\Delivery\ShoppingCartDeliveryRepository;
use App\ShoppingCart\Delivery\ShoppingCartDeliveryRepositoryFactory;
use App\ShoppingCart\Delivery\ShoppingCartDeliverySaveFacadeException;
use App\ShoppingCart\Delivery\ShoppingCartDeliverySaveFacadeFactory;
use App\ShoppingCart\ShoppingCart;
use App\ShoppingCart\ShoppingCartHash;
use App\ShoppingCart\ShoppingCartRepository;
use App\ShoppingCart\ShoppingCartRepositoryFactory;
use App\ShoppingCart\ShoppingCartTranslation;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);


class ShoppingCartDeliverySaveFacadeTest extends BaseTestCase
{


    /** @var DeliveryRepository|null */
    protected $deliveryRepo;

    /** @var ShoppingCartRepository|null */
    protected $shoppingCartRepo;

    /** @var ShoppingCartDeliveryRepository|null */
    protected $shoppingCartDeliveryRepo;

    /** @var Delivery|null */
    protected $allowedDelivery;

    /** @var Delivery|null */
    protected $forbiddenDelivery;

    /** @var ShoppingCart|null */
    protected $shoppingCart;



    public function setUp()
    {
        parent::setUp();

        $this->shoppingCartDeliveryRepo = $this->container->getByType(ShoppingCartDeliveryRepositoryFactory::class)->create();

        //save test delivery
        $deliveryRepoFactory = $this->container->getByType(DeliveryRepositoryFactory::class);
        $deliveryTranslationRepoFactory = $this->container->getByType(DeliveryTranslationRepositoryFactory::class);
        $this->deliveryRepo = $deliveryRepoFactory->create();
        $deliveryTranslationRepo = $deliveryTranslationRepoFactory->create();

        $delivery = new Delivery();
        $delivery->setExternalSystemId(1);
        $delivery->setState(Delivery::FORBIDDEN);
        $delivery->setSort(1);
        $delivery->setPrice(100.50);
        $delivery->setVat(50);
        $this->deliveryRepo->save($delivery);
        $this->forbiddenDelivery = $delivery;

        $delivery = new Delivery();
        $delivery->setExternalSystemId(2);
        $delivery->setState(Delivery::ALLOWED);
        $delivery->setSort(2);
        $delivery->setPrice(250.50);
        $delivery->setVat(20);
        $this->deliveryRepo->save($delivery);
        $this->allowedDelivery = $delivery;

        $deliveryTranslation1 = new DeliveryTranslation();
        $deliveryTranslation1->setDeliveryId($this->allowedDelivery->getId());
        $deliveryTranslation1->setLanguageId(1);
        $deliveryTranslation1->setName('PPL - kurýr');
        $deliveryTranslation2 = new DeliveryTranslation();
        $deliveryTranslation2->setDeliveryId($this->allowedDelivery->getId());
        $deliveryTranslation2->setLanguageId(2);
        $deliveryTranslation2->setName('PPL - courier');
        $translations = [$deliveryTranslation1, $deliveryTranslation2];
        $deliveryTranslationRepo->save($translations);

        $this->allowedDelivery->setTranslations($translations);

        //save test shopping cart
        $shoppingCartRepoFactory = $this->container->getByType(ShoppingCartRepositoryFactory::class);
        $this->shoppingCartRepo = $shoppingCartRepoFactory->create();

        $shoppingCart = new ShoppingCart();
        $shoppingCart->setIpAddress('::1');
        $shoppingCart->setName('Peter Tester');
        $shoppingCart->setEmail('peter@tester.cz');
        $shoppingCart->setBirthdayCoupon(TRUE);
        $shoppingCart->setHash(ShoppingCartHash::generateHash());
        $this->shoppingCartRepo->save($shoppingCart);
        $this->shoppingCart = $shoppingCart;
    }



    public function testSaveNewDelivery()
    {
        /** @var $saveFacadeFactory ShoppingCartDeliverySaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartDeliverySaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $shoppingCartDelivery = $saveFacade->save((int)$this->shoppingCart->getId(), (int)$this->allowedDelivery->getId());

        //load delivery from storage
        $shoppingCartDeliveryFromStorage = $this->shoppingCartDeliveryRepo->getOneByShoppingCartId((int)$shoppingCartDelivery->getShoppingCartId());

        Assert::type(ShoppingCartDelivery::class, $shoppingCartDelivery);
        Assert::same((int)$this->shoppingCart->getId(), (int)$shoppingCartDelivery->getShoppingCartId());
        Assert::same((int)$this->allowedDelivery->getId(), (int)$shoppingCartDelivery->getDeliveryId());
        Assert::same(0.0, (float)$shoppingCartDelivery->getDiscount());
        Assert::same((float)$this->allowedDelivery->getPrice(), (float)$shoppingCartDelivery->getPrice());
        Assert::same((float)$this->allowedDelivery->getVat(), (float)$shoppingCartDelivery->getVat());

        Assert::type(ShoppingCartDelivery::class, $shoppingCartDeliveryFromStorage);
        Assert::same((int)$this->shoppingCart->getId(), (int)$shoppingCartDeliveryFromStorage->getShoppingCartId());
        Assert::same((int)$this->allowedDelivery->getId(), (int)$shoppingCartDeliveryFromStorage->getDeliveryId());
        Assert::same(0.0, (float)$shoppingCartDeliveryFromStorage->getDiscount());
        Assert::same((float)$this->allowedDelivery->getPrice(), (float)$shoppingCartDeliveryFromStorage->getPrice());
        Assert::same((float)$this->allowedDelivery->getVat(), (float)$shoppingCartDeliveryFromStorage->getVat());
    }



    public function testSaveWithoutBirthdayCouponDiscount()
    {
        $this->shoppingCart->setBirthdayCoupon(FALSE);
        $this->shoppingCartRepo->save($this->shoppingCart);

        /** @var $saveFacadeFactory ShoppingCartDeliverySaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartDeliverySaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $shoppingCartDelivery = $saveFacade->save((int)$this->shoppingCart->getId(), (int)$this->allowedDelivery->getId());

        //load delivery from storage
        $shoppingCartDeliveryFromStorage = $this->shoppingCartDeliveryRepo->getOneByShoppingCartId((int)$shoppingCartDelivery->getShoppingCartId());

        Assert::type(ShoppingCartDelivery::class, $shoppingCartDelivery);
        Assert::same((int)$this->shoppingCart->getId(), (int)$shoppingCartDelivery->getShoppingCartId());
        Assert::same((int)$this->allowedDelivery->getId(), (int)$shoppingCartDelivery->getDeliveryId());
        Assert::same(0.0, (float)$shoppingCartDelivery->getDiscount());
        Assert::same((float)$this->allowedDelivery->getPrice(), (float)$shoppingCartDelivery->getPrice());
        Assert::same((float)$this->allowedDelivery->getVat(), (float)$shoppingCartDelivery->getVat());

        Assert::type(ShoppingCartDelivery::class, $shoppingCartDeliveryFromStorage);
        Assert::same((int)$this->shoppingCart->getId(), (int)$shoppingCartDeliveryFromStorage->getShoppingCartId());
        Assert::same((int)$this->allowedDelivery->getId(), (int)$shoppingCartDeliveryFromStorage->getDeliveryId());
        Assert::same(0.0, (float)$shoppingCartDeliveryFromStorage->getDiscount());
        Assert::same((float)$this->allowedDelivery->getPrice(), (float)$shoppingCartDeliveryFromStorage->getPrice());
        Assert::same((float)$this->allowedDelivery->getVat(), (float)$shoppingCartDeliveryFromStorage->getVat());
    }



    public function testSaveWithNotExistsDelivery()
    {
        $deliveryId = (int)$this->allowedDelivery->getId() + 1;

        /** @var $saveFacadeFactory ShoppingCartDeliverySaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartDeliverySaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade, $deliveryId) {
            $saveFacade->save((int)$this->shoppingCart->getId(), $deliveryId);
        }, ShoppingCartDeliverySaveFacadeException::class, sprintf('%s.delivery.not.found', ShoppingCartTranslation::getFileName()));
    }



    public function testSaveForNotExistsShoppingCart()
    {
        $shoppingCartId = (int)$this->shoppingCart->getId() + 1;

        /** @var $saveFacadeFactory ShoppingCartDeliverySaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartDeliverySaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade, $shoppingCartId) {
            $saveFacade->save($shoppingCartId, (int)$this->allowedDelivery->getId());
        }, ShoppingCartDeliverySaveFacadeException::class, sprintf('%s.action.failed', ShoppingCartTranslation::getFileName()));
    }



    public function tearDown()
    {
        parent::tearDown();

        $this->shoppingCartRepo->remove($this->shoppingCart);
        $this->deliveryRepo->remove($this->allowedDelivery);
        $this->deliveryRepo->remove($this->forbiddenDelivery);

        $this->shoppingCart = NULL;
        $this->allowedDelivery = NULL;
        $this->forbiddenDelivery = NULL;
    }
}

(new ShoppingCartDeliverySaveFacadeTest())->run();