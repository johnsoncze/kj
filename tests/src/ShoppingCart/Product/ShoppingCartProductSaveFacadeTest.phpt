<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Product;

use App\Product\Product;
use App\Product\ProductFactory;
use App\Product\ProductRepository;
use App\Product\ProductRepositoryFactory;
use App\Product\Translation\ProductTranslationFactory;
use App\Product\Translation\ProductTranslationRepositoryFactory;
use App\ProductState\ProductStateRepository;
use App\ShoppingCart\Product\ShoppingCartProduct;
use App\ShoppingCart\Product\ShoppingCartProductRepository;
use App\ShoppingCart\Product\ShoppingCartProductRepositoryFactory;
use App\ShoppingCart\Product\ShoppingCartProductSaveFacadeException;
use App\ShoppingCart\Product\ShoppingCartProductSaveFacadeFactory;
use App\ShoppingCart\ShoppingCart;
use App\ShoppingCart\ShoppingCartDiscount;
use App\ShoppingCart\ShoppingCartHash;
use App\ShoppingCart\ShoppingCartRepository;
use App\ShoppingCart\ShoppingCartRepositoryFactory;
use App\ShoppingCart\ShoppingCartTranslation;
use App\Tests\BaseTestCase;
use App\Tests\ProductState\ProductStateTestTrait;
use Kdyby\Translation\ITranslator;
use Nette\Database\Context;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ShoppingCartProductSaveFacadeTest extends BaseTestCase
{

	use ProductStateTestTrait;


    /** @var ProductRepository|null */
    protected $productRepo;

    /** @var ProductStateRepository */
    protected $productStateRepo;

    /** @var ShoppingCartRepository|null */
    protected $shoppingCartRepo;

    /** @var ShoppingCartProductRepository|null */
    protected $shoppingCartProductRepo;

    /** @var ShoppingCart|null */
    protected $shoppingCart;

    /** @var Product[]|array */
    protected $products = [];



    public function setUp()
    {
        parent::setUp();

        $database = $this->container->getByType(Context::class);
        $this->productStateRepo = $this->container->getByType(ProductStateRepository::class);
        $shoppingCartProductRepoFactory = $this->container->getByType(ShoppingCartProductRepositoryFactory::class);
        $this->shoppingCartProductRepo = $shoppingCartProductRepoFactory->create();

        //test state
		$productState = $this->createTestProductState();
		$this->productStateRepo->save($productState);
		$this->addEntityForRemove($productState, $this->productStateRepo);

        //save test products
        $productRepoFactory = $this->container->getByType(ProductRepositoryFactory::class);
        $this->productRepo = $productRepoFactory->create();
        $productFactory = new ProductFactory();
        $product1 = $productFactory->create('CZ450', NULL, $productState->getId(), $productState->getId(), 12, 450.50, 21.00, Product::DRAFT, TRUE, TRUE);
        $product1->setExternalSystemId(1);
        $product2 = $productFactory->create('CZ330', NULL, $productState->getId(), $productState->getId(), 12, 330.30, 21.00, Product::PUBLISH, TRUE, TRUE);
        $product2->setExternalSystemId(2);
        $product3 = $productFactory->create('CZ220', NULL, $productState->getId(), $productState->getId(), 12, 220.20, 21.00, Product::PUBLISH, TRUE, TRUE);
        $product3->setExternalSystemId(3);
        $product4 = $productFactory->create('CZ110', NULL, $productState->getId(), $productState->getId(), 12, 110.10, 21.00, Product::PUBLISH, TRUE, FALSE);
        $product4->setExternalSystemId(4);
        $products = [$product1, $product2, $product3, $product4];
        $database->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->productRepo->save($products);
        $database->query('SET FOREIGN_KEY_CHECKS = 1');
        $this->products = $products;

        $productTranslationFactory = new ProductTranslationFactory();
        $translation1 = $productTranslationFactory->create((int)$product2->getId(), 1, 'Produkt modrý', NULL, '/produkt-modry');
        $translation2 = $productTranslationFactory->create((int)$product2->getId(), 2, 'Product blue', NULL, '/product-blue');

        $translation3 = $productTranslationFactory->create((int)$product3->getId(), 1, 'Produkt růžový', NULL, '/produkt-ruzovy');
        $translation4 = $productTranslationFactory->create((int)$product3->getId(), 2, 'Product pink', NULL, '/product-pink');

        $translation5 = $productTranslationFactory->create((int)$product4->getId(), 1, 'Produkt žlutý', NULL, '/produkt-zluty');
        $translation6 = $productTranslationFactory->create((int)$product4->getId(), 2, 'Product yellow', NULL, '/product-yellow');

        $productTranslationRepoFactory = $this->container->getByType(ProductTranslationRepositoryFactory::class);
        $productTranslationRepo = $productTranslationRepoFactory->create();
        $productTranslationRepo->save([$translation1, $translation2, $translation3, $translation4, $translation5, $translation6]);

        $product2->setTranslations([$translation1, $translation2]);
        $product3->setTranslations([$translation3, $translation4]);
        $product4->setTranslations([$translation5, $translation6]);

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



    /************************ Save a new product ************************/

    public function testSaveNew()
    {
        $product = $this->products[1];
        $quantity = 10;
        $shoppingCartId = (int)$this->shoppingCart->getId();
        $productId = (int)$product->getId();

        /** @var $saveFacadeFactory ShoppingCartProductSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartProductSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $shoppingCartProduct = $saveFacade->save($shoppingCartId, $productId, $quantity);

        //load the shopping cart product from storage
        $cartProductFromStorage = $this->shoppingCartProductRepo->getOneById((int)$shoppingCartProduct->getId(), $this->container->getByType(ITranslator::class));

        Assert::type(ShoppingCartProduct::class, $shoppingCartProduct);
        Assert::same($shoppingCartId, (int)$shoppingCartProduct->getShoppingCartId());
        Assert::same($productId, (int)$shoppingCartProduct->getProductId());
        Assert::same($quantity, (int)$shoppingCartProduct->getQuantity());
        Assert::same(ShoppingCartDiscount::BIRTHDAY_COUPON_DISCOUNT, (float)$shoppingCartProduct->getDiscount());
        Assert::same((float)$product->getPrice(), (float)$shoppingCartProduct->getPrice());
        Assert::same((float)$product->getVat(), (float)$shoppingCartProduct->getVat());

        Assert::type(ShoppingCartProduct::class, $cartProductFromStorage);
        Assert::same($shoppingCartId, (int)$cartProductFromStorage->getShoppingCartId());
        Assert::same($productId, (int)$cartProductFromStorage->getProductId());
        Assert::same($quantity, (int)$cartProductFromStorage->getQuantity());
        Assert::same(ShoppingCartDiscount::BIRTHDAY_COUPON_DISCOUNT, (float)$cartProductFromStorage->getDiscount());
        Assert::same((float)$product->getPrice(), (float)$cartProductFromStorage->getPrice());
        Assert::same((float)$product->getVat(), (float)$cartProductFromStorage->getVat());
    }



    public function testSaveNewWithWrongQuantity()
    {
        /** @var $saveFacadeFactory ShoppingCartProductSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartProductSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->save((int)$this->shoppingCart->getId(), (int)$this->products[1]->getId(), 0);
        }, ShoppingCartProductSaveFacadeException::class, 'Quantity can not be less or equal than 0.');

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->save((int)$this->shoppingCart->getId(), (int)$this->products[1]->getId(), -10);
        }, ShoppingCartProductSaveFacadeException::class, 'Quantity can not be less or equal than 0.');
    }



    public function testSaveNewForUnknownShoppingCart()
    {
        /** @var $saveFacadeFactory ShoppingCartProductSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartProductSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->save((int)$this->shoppingCart->getId() + 1, (int)$this->products[1]->getId(), 10);
        }, ShoppingCartProductSaveFacadeException::class, sprintf('%s.action.failed', ShoppingCartTranslation::getFileName()));
    }



    public function testSaveNewNotPublishedProduct()
    {
        /** @var $saveFacadeFactory ShoppingCartProductSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartProductSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->save((int)$this->shoppingCart->getId(), (int)$this->products[0]->getId(), 10);
        }, ShoppingCartProductSaveFacadeException::class, sprintf('%s.product.not.found', ShoppingCartTranslation::getFileName()));
    }



    public function testSaveNenWithUnknownProduct()
    {
        /** @var $saveFacadeFactory ShoppingCartProductSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartProductSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->save((int)$this->shoppingCart->getId(), (int)end($this->products)->getId() + 1, 10);
        }, ShoppingCartProductSaveFacadeException::class, sprintf('%s.product.not.found', ShoppingCartTranslation::getFileName()));
    }



    /************************ Update the product ************************/

    public function testUpdate()
    {
        $product = $this->products[1];
        $quantity = 10;
        $shoppingCartId = (int)$this->shoppingCart->getId();
        $productId = (int)$product->getId();

        /** @var $saveFacadeFactory ShoppingCartProductSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartProductSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        //save a new
        $shoppingCartProduct = $saveFacade->save($shoppingCartId, $productId, $quantity);

        //update
        $shoppingCartProduct = $saveFacade->update((int)$shoppingCartProduct->getId(), $quantity);

        //load the shopping cart product from storage
        $cartProductFromStorage = $this->shoppingCartProductRepo->getOneById((int)$shoppingCartProduct->getId(), $this->container->getByType(ITranslator::class));

        Assert::type(ShoppingCartProduct::class, $shoppingCartProduct);
        Assert::same($shoppingCartId, (int)$shoppingCartProduct->getShoppingCartId());
        Assert::same($productId, (int)$shoppingCartProduct->getProductId());
        Assert::same($quantity, (int)$shoppingCartProduct->getQuantity());
        Assert::same(ShoppingCartDiscount::BIRTHDAY_COUPON_DISCOUNT, (float)$shoppingCartProduct->getDiscount());
        Assert::same((float)$product->getPrice(), (float)$shoppingCartProduct->getPrice());
        Assert::same((float)$product->getVat(), (float)$shoppingCartProduct->getVat());

        Assert::type(ShoppingCartProduct::class, $cartProductFromStorage);
        Assert::same($shoppingCartId, (int)$cartProductFromStorage->getShoppingCartId());
        Assert::same($productId, (int)$cartProductFromStorage->getProductId());
        Assert::same($quantity, (int)$cartProductFromStorage->getQuantity());
        Assert::same(ShoppingCartDiscount::BIRTHDAY_COUPON_DISCOUNT, (float)$cartProductFromStorage->getDiscount());
        Assert::same((float)$product->getPrice(), (float)$cartProductFromStorage->getPrice());
        Assert::same((float)$product->getVat(), (float)$cartProductFromStorage->getVat());
    }



    public function testUpdateWrongQuantity()
    {
        $product = $this->products[1];
        $quantity = 10;
        $shoppingCartId = (int)$this->shoppingCart->getId();
        $productId = (int)$product->getId();

        /** @var $saveFacadeFactory ShoppingCartProductSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartProductSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        //save a new
        $shoppingCartProduct = $saveFacade->save($shoppingCartId, $productId, $quantity);

        Assert::exception(function () use ($saveFacade, $shoppingCartProduct) {
            $saveFacade->update((int)$shoppingCartProduct->getId(), 0);
        }, ShoppingCartProductSaveFacadeException::class, sprintf('%s.product.wrong.quantity', ShoppingCartTranslation::getFileName()));

        Assert::exception(function () use ($saveFacade, $shoppingCartProduct) {
            $saveFacade->update((int)$shoppingCartProduct->getId(), -10);
        }, ShoppingCartProductSaveFacadeException::class, sprintf('%s.product.wrong.quantity', ShoppingCartTranslation::getFileName()));
    }



    public function testUpdateUnknownProduct()
    {
        /** @var $saveFacadeFactory ShoppingCartProductSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartProductSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->update((int)$this->products[1]->getId(), 10);
        }, ShoppingCartProductSaveFacadeException::class, sprintf('%s.product.not.found', ShoppingCartTranslation::getFileName()));
    }



    public function tearDown()
    {
        //remove test shopping cart
        $this->shoppingCartRepo->remove($this->shoppingCart);
        $this->shoppingCart = NULL;

        //remove test products
        foreach ($this->products as $product) {
            $this->productRepo->remove($product);
        }
        $this->products = [];

		parent::tearDown();
    }
}

(new ShoppingCartProductSaveFacadeTest())->run();