<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Product;

use App\ShoppingCart\Product\ShoppingCartProduct;
use App\ShoppingCart\Product\ShoppingCartProductDeleteFacadeException;
use App\ShoppingCart\Product\ShoppingCartProductDeleteFacadeFactory;
use App\ShoppingCart\Product\ShoppingCartProductHash;
use App\ShoppingCart\Product\ShoppingCartProductNotFoundException;
use App\ShoppingCart\Product\ShoppingCartProductRepository;
use App\ShoppingCart\Product\ShoppingCartProductRepositoryFactory;
use App\ShoppingCart\ShoppingCartHash;
use App\ShoppingCart\ShoppingCartTranslation;
use App\Tests\BaseTestCase;
use Kdyby\Translation\ITranslator;
use Nette\Database\Context;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartProductDeleteFacadeTest extends BaseTestCase
{


    /** @var ShoppingCartProduct|null */
    protected $product;

    /** @var ShoppingCartProductRepository|null */
    protected $productRepo;



    protected function setUp()
    {
        parent::setUp();

        //save test product
        $hash = ShoppingCartHash::generateHash();
        $product = new ShoppingCartProduct();
        $product->setName('product');
        $product->setShoppingCartId(5);
        $product->setProductId(77);
        $product->setPrice(450.50);
        $product->setVat(21.00);
        $product->setQuantity(55);
        $product->setDiscount(15.00);
        $product->setHash($hash);

        $database = $this->container->getByType(Context::class);
        $productRepoFactory = $this->container->getByType(ShoppingCartProductRepositoryFactory::class);
        $this->productRepo = $productRepoFactory->create();
        $database->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->productRepo->save($product);
        $database->query('SET FOREIGN_KEY_CHECKS = 1');
        $this->product = $product;
    }



    public function testDelete()
    {
        /** @var $deleteFacadeFactory ShoppingCartProductDeleteFacadeFactory */
        $deleteFacadeFactory = $this->container->getByType(ShoppingCartProductDeleteFacadeFactory::class);
        $deleteFacade = $deleteFacadeFactory->create();

        Assert::true($deleteFacade->delete((int)$this->product->getShoppingCartId(), $this->product->getHash()));
        Assert::exception(function () {
            $this->productRepo->getOneById((int)$this->product->getId(), $this->container->getByType(ITranslator::class));
        }, ShoppingCartProductNotFoundException::class, sprintf('%s.product.not.found', ShoppingCartTranslation::getFileName()));
    }



    public function testDeleteNotExistsProduct()
    {
        /** @var $deleteFacadeFactory ShoppingCartProductDeleteFacadeFactory */
        $deleteFacadeFactory = $this->container->getByType(ShoppingCartProductDeleteFacadeFactory::class);
        $deleteFacade = $deleteFacadeFactory->create();

        Assert::exception(function () use ($deleteFacade) {
            $deleteFacade->delete((int)$this->product->getShoppingCartId() + 1, ShoppingCartProductHash::generateHash());
        }, ShoppingCartProductDeleteFacadeException::class, sprintf('%s.product.not.found', ShoppingCartTranslation::getFileName()));
    }



    protected function tearDown()
    {
        parent::tearDown();
        $this->productRepo->remove($this->product);
    }
}

(new ShoppingCartProductDeleteFacadeTest())->run();