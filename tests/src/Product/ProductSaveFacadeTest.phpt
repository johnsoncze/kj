<?php

declare(strict_types = 1);

namespace App\Tests\Product;

use App\Product\Product;
use App\Product\ProductFactory;
use App\Product\ProductRepository;
use App\Product\ProductRepositoryFactory;
use App\Product\ProductSaveFacade;
use App\Product\ProductSaveFacadeException;
use App\Product\ProductSaveFacadeFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock("database", TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductSaveFacadeTest extends BaseTestCase
{


    /** @var array|Product[] */
    protected $products = [];

    /** @var null|ProductRepository */
    protected $productRepo;

    /** @var ProductSaveFacade */
    protected $productSaveFacade;



    public function setUp()
    {
        parent::setUp();

        //save a test product
        $productFactory = new ProductFactory();
        $this->products[] = $product = $productFactory->create('znh77-778', NULL, 1, 1, 57, 55.55, 21.000, Product::PUBLISH, TRUE, TRUE);
        $product->setExternalSystemId(123);
        $product->setCompleted(1);

        $productRepoFactory = $this->container->getByType(ProductRepositoryFactory::class);
        $saveFacadeFactory = $this->container->getByType(ProductSaveFacadeFactory::class);
        $this->productSaveFacade = $saveFacadeFactory->create();

        $this->productRepo = $productRepoFactory->create();
        $this->productRepo->save($product);
    }



    public function testAddNewProduct()
    {
        $code = 'kk777sss-0';
        $externalSystemId = 321;
        $stockState = 1;
        $emptyStockState = 2;
        $stock = 40;
        $price = 788.440;
        $vat = 21.000;
        $state = Product::PUBLISH;
        $new = FALSE;
        $saleOnline = TRUE;

        $product = $this->productSaveFacade->saveNew($code, $externalSystemId, $stockState, $emptyStockState, $stock, $price, $vat, $state, $new, $saleOnline);

        //load product from storage
        $this->products[] = $productFromStorage = $this->productRepo->getOneById($product->getId());

        Assert::type(Product::class, $product);
        Assert::type(Product::class, $productFromStorage);

        foreach ([$product, $productFromStorage] as $productObject) {
            Assert::same($code, $productObject->getCode());
            Assert::same($externalSystemId, (int)$productObject->getExternalSystemId());
            Assert::same($stockState, (int)$productObject->getStockState());
            Assert::same($emptyStockState, (int)$productObject->getEmptyStockState());
            Assert::same($stock, (int)$productObject->getStock());
            Assert::same($price, (float)$productObject->getPrice());
            Assert::same($vat, (float)$productObject->getVat());
            Assert::same($state, $productObject->getState());
            Assert::same($new, (bool)$productObject->getNew());
            Assert::same($saleOnline, (bool)$productObject->getSaleOnline());
        }
    }



    public function testAddNewProductWithBadStates()
    {
        Assert::exception(function () {
            $this->productSaveFacade->saveNew('zmms4445', 3, 5, 2, 50, 58.78, 20, Product::PUBLISH, FALSE, TRUE);
        }, ProductSaveFacadeException::class, sprintf('Status produktů s id "%d" nebyl nalezen.', 5));

        Assert::exception(function () {
            $this->productSaveFacade->saveNew('zmms4445', 3, 1, 6, 50, 58.78, 20, Product::PUBLISH, FALSE, TRUE);
        }, ProductSaveFacadeException::class, sprintf('Status produktů s id "%d" nebyl nalezen.', 6));
    }



    public function testAddNewProductWithBadPrice()
    {
        Assert::exception(function () {
            $this->productSaveFacade->saveNew('zmms4445', 33, 1, 2, 50, -58.78, 21.000, Product::PUBLISH, FALSE, TRUE);
        }, ProductSaveFacadeException::class, 'Cena musí být větší než 0.');
    }



    public function testUpdateProduct()
    {
        $code = 'kk777sss-0';
        $externalSystemId = 355;
        $stockState = 1;
        $emptyStockState = 2;
        $stock = 40;
        $price = 788.440;
        $vat = 21.000;
        $state = Product::PUBLISH;
        $new = FALSE;
        $saleOnline = TRUE;

        $product = $this->productSaveFacade->update(end($this->products)->getId(), $externalSystemId, $code, $stockState, $emptyStockState, $stock, $price, $vat, $state, $new, $saleOnline);
        $productFromStorage = $this->productRepo->getOneById($product->getId());

        Assert::type(Product::class, $product);
        Assert::type(Product::class, $productFromStorage);

        foreach ([$product, $productFromStorage] as $productObject) {
            Assert::same($code, $productObject->getCode());
            Assert::same($externalSystemId, (int)$productObject->getExternalSystemId());
            Assert::same($stockState, (int)$productObject->getStockState());
            Assert::same($emptyStockState, (int)$productObject->getEmptyStockState());
            Assert::same($stock, (int)$productObject->getStock());
            Assert::same($price, (float)$productObject->getPrice());
            Assert::same($vat, (float)$productObject->getVat());
            Assert::same($state, $productObject->getState());
            Assert::same($new, (bool)$productObject->getNew());
            Assert::same($saleOnline, (bool)$productObject->getSaleOnline());
        }
    }



    public function testUpdateNotExistsProduct()
    {
        $productId = (int)(end($this->products)->getId() + 20);

        Assert::exception(function () use ($productId) {
            $this->productSaveFacade->update($productId, 453, 'zmms4445', 1, 2, 50, 58.78, 21.000, Product::PUBLISH, FALSE, TRUE);
        }, ProductSaveFacadeException::class, sprintf('Produkt s id "%d" nebyl nalezen.', $productId));
    }



    public function testUpdateProductWithBadPrice()
    {
        $productId = (int)(end($this->products)->getId() + 20);

        Assert::exception(function () use ($productId) {
            $this->productSaveFacade->update((int)end($this->products)->getId(), 444, 'zmms4445', 1, 2, 50, -58.78, 21.000, Product::PUBLISH, FALSE, TRUE);
        }, ProductSaveFacadeException::class, 'Cena musí být větší než 0.');
    }



    public function testUpdateProductWithBadStates()
    {
        $productId = (int)end($this->products)->getId();

        Assert::exception(function () use ($productId) {
            $this->productSaveFacade->update($productId, 333, 'zmms4445', 5, 2, 50, 58.78, 21.000, Product::PUBLISH, FALSE, TRUE);
        }, ProductSaveFacadeException::class, sprintf('Status produktů s id "%d" nebyl nalezen.', 5));

        Assert::exception(function () use ($productId) {
            $this->productSaveFacade->update($productId, 543, 'zmms4445', 1, 6, 50, 58.78, 21.000, Product::PUBLISH, FALSE, TRUE);
        }, ProductSaveFacadeException::class, sprintf('Status produktů s id "%d" nebyl nalezen.', 6));
    }



    public function testUpdateByExternalSystemIdWithUnknownProduct()
    {
        $externalSystemId = 456;

        Assert::exception(function () use ($externalSystemId) {
            $this->productSaveFacade->updateByExternalSystemId($externalSystemId, 'ZPTO123', 3500.50, 21.000);
        }, ProductSaveFacadeException::class, sprintf('Produkt s externím id \'%s\' nebyl nalezen.', $externalSystemId));
    }



    public function testUpdateByExternalSystemIdWithUnknownVat()
    {
        Assert::exception(function () {
            $this->productSaveFacade->updateByExternalSystemId((int)$this->products[0]->getExternalSystemId(), 'ABC123', 3500.50, 19.000);
        }, ProductSaveFacadeException::class, 'Neznámé DPH.');
    }



    public function testUpdateByExternalSystemIdSuccess()
    {
        $product = $this->products[0];
        $externalSystemId = (int)$product->getExternalSystemId();
        $code = 'ABC123';
        $price = 87650.500;
        $vat = 21.000;

        $response = $this->productSaveFacade->updateByExternalSystemId($externalSystemId, $code, $price, $vat);
        $productFromStorage = $this->productRepo->getOneById((int)$product->getId());

        Assert::type(Product::class, $response);
        Assert::type(Product::class, $productFromStorage);

        foreach ([$response, $productFromStorage] as $productObject) {
            Assert::same($code, $productObject->getCode());
            Assert::same($externalSystemId, (int)$productObject->getExternalSystemId());
            Assert::same($product->getStockState(), (int)$productObject->getStockState());
            Assert::same($product->getEmptyStockState(), (int)$productObject->getEmptyStockState());
            Assert::same($product->getStock(), (int)$productObject->getStock());
            Assert::same($price, (float)$productObject->getPrice());
            Assert::same($vat, (float)$productObject->getVat());
            Assert::same($product->getState(), $productObject->getState());
            Assert::same($product->isNew(), $productObject->isNew());
            Assert::same($product->isCompleted(), $productObject->isCompleted());
            Assert::same($product->canBeSellOnline(), (bool)$productObject->getSaleOnline());
            Assert::same($product->getGoogleMerchantCategory(), $productObject->getGoogleMerchantCategory());
            Assert::same($product->getGoogleMerchantBrandText(), $productObject->getGoogleMerchantBrandText());
            Assert::same($product->getGoogleMerchantBrand(), $productObject->getGoogleMerchantBrand());
        }
    }



    public function tearDown()
    {
        parent::tearDown();

        //remove test products
        foreach ($this->products as $product) {
            $this->productRepo->remove($product);
        }
        $this->products = [];
    }
}

(new ProductSaveFacadeTest())->run();