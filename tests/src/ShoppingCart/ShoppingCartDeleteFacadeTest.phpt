<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart;

use App\ShoppingCart\ShoppingCart;
use App\ShoppingCart\ShoppingCartDeleteFacadeException;
use App\ShoppingCart\ShoppingCartDeleteFacadeFactory;
use App\ShoppingCart\ShoppingCartHash;
use App\ShoppingCart\ShoppingCartIpAddress;
use App\ShoppingCart\ShoppingCartNotFoundException;
use App\ShoppingCart\ShoppingCartRepository;
use App\ShoppingCart\ShoppingCartRepositoryFactory;
use App\ShoppingCart\ShoppingCartTranslation;
use App\Tests\BaseTestCase;
use Kdyby\Translation\ITranslator;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartDeleteFacadeTest extends BaseTestCase
{


    /** @var ShoppingCartRepository|null */
    protected $shoppingCartRepo;

    /** @var ShoppingCart|null */
    protected $shoppingCart;



    protected function setUp()
    {
        parent::setUp();

        //save a test cart
        $shoppingCart = new ShoppingCart();
        $shoppingCart->setIpAddress(ShoppingCartIpAddress::CONSOLE);
        $shoppingCart->setHash(ShoppingCartHash::generateHash());
        $shoppingCartRepoFactory = $this->container->getByType(ShoppingCartRepositoryFactory::class);
        $this->shoppingCartRepo = $shoppingCartRepoFactory->create();
        $this->shoppingCart = $this->shoppingCartRepo->save($shoppingCart);
    }



    public function testDelete()
    {
        $deleteFacadeFactory = $this->container->getByType(ShoppingCartDeleteFacadeFactory::class);
        $deleteFacade = $deleteFacadeFactory->create();
        $deleteFacade->delete((int)$this->shoppingCart->getId());

        Assert::exception(function () {
            $this->shoppingCartRepo->getOneById((int)$this->shoppingCart->getId(), $this->container->getByType(ITranslator::class));
        }, ShoppingCartNotFoundException::class, sprintf('%s.not.found', ShoppingCartTranslation::getFileName()));
    }



    public function testDeleteUnknownShoppingCart()
    {
        $deleteFacadeFactory = $this->container->getByType(ShoppingCartDeleteFacadeFactory::class);
        $deleteFacade = $deleteFacadeFactory->create();

        Assert::exception(function () use ($deleteFacade) {
            $deleteFacade->delete((int)$this->shoppingCart->getId() + 1);
        }, ShoppingCartDeleteFacadeException::class, sprintf('%s.deleted.already', ShoppingCartTranslation::getFileName()));
    }



    protected function tearDown()
    {
        parent::tearDown();

        //delete the test shopping cart
        $this->shoppingCartRepo->remove($this->shoppingCart);
        $this->shoppingCart = NULL;
    }
}

(new ShoppingCartDeleteFacadeTest())->run();