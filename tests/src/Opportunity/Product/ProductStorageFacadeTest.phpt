<?php

declare(strict_types = 1);

namespace App\Tests\Opportunity\Product;

require_once __DIR__ . '/../../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

use App\Opportunity\OpportunityRepository;
use App\Opportunity\Product\Product;
use App\Opportunity\Product\ProductRepository;
use App\Opportunity\Product\ProductStorageFacade;
use App\Opportunity\Product\ProductStorageFacadeException;
use App\Opportunity\Product\ProductStorageFacadeFactory;
use App\Product\ProductRepository AS CatalogProductRepository;
use App\Product\Translation\ProductTranslationRepository AS CatalogProductTranslationRepository;
use App\Tests\BaseTestCase;
use App\Tests\Opportunity\OpportunityTestTrait;
use App\Tests\Product\ProductTestTrait;
use App\Tests\Product\Translation\ProductTranslationTestTrait;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductStorageFacadeTest extends BaseTestCase
{


    use OpportunityTestTrait;
    use ProductTestTrait;
    use ProductTranslationTestTrait;

    /** @var CatalogProductRepository */
    private $catalogProductRepo;

    /** @var CatalogProductTranslationRepository */
    private $catalogProductTranslationRepo;

    /** @var OpportunityRepository */
    private $opportunityRepo;

    /** @var ProductRepository */
    private $productRepo;

    /** @var ProductStorageFacade */
    private $productStorageFacade;



    protected function setUp()
    {
        parent::setUp();
        $this->catalogProductRepo = $this->container->getByType(CatalogProductRepository::class);
        $this->catalogProductTranslationRepo = $this->container->getByType(CatalogProductTranslationRepository::class);
        $this->opportunityRepo = $this->container->getByType(OpportunityRepository::class);
        $this->productRepo = $this->container->getByType(ProductRepository::class);
        $productStorageFacadeFactory = $this->container->getByType(ProductStorageFacadeFactory::class);
        $this->productStorageFacade = $productStorageFacadeFactory->create();
    }



    /**
     * @dataProvider getStockLoop
     * @param $stock int
     * @param $expectedStock bool
     */
    public function testAddSuccess(int $stock, bool $expectedStock)
    {
        $opportunity = $this->createTestOpportunity();
        $opportunity->setCustomerId(NULL);
        $this->saveWithoutForeignKeysCheck($opportunity, $this->opportunityRepo);
        $this->addEntityForRemove($opportunity, $this->opportunityRepo);

        $catalogProduct = $this->createTestProduct();
        $catalogProduct->setStock($stock);
        $catalogProduct->setState($catalogProduct::PUBLISH);
        $this->catalogProductRepo->save($catalogProduct);
        $this->addEntityForRemove($catalogProduct, $this->catalogProductRepo);

        $catalogProductTranslation = $this->createTestProductTranslation();
        $catalogProductTranslation->setProductId($catalogProduct->getId());
        $this->catalogProductTranslationRepo->save($catalogProductTranslation);

        $response = $this->productStorageFacade->add($opportunity->getId(), $catalogProduct->getId(), 5);
        $productFromStorage = $this->productRepo->findByOpportunityId($opportunity->getId());

        Assert::type(Product::class, $response);
        Assert::count(1, $productFromStorage);
        Assert::type(Product::class, end($productFromStorage));

        /** @var $product Product */
        foreach ([$response, end($productFromStorage)] as $product) {
            Assert::same($opportunity->getId(), (int)$product->getOpportunityId());
            Assert::same($catalogProduct->getId(), (int)$product->getProductId());
            Assert::same($catalogProduct->getExternalSystemId(), (int)$product->getExternalSystemId());
            Assert::same($catalogProductTranslation->getName(), $product->getName());
            Assert::same($catalogProductTranslation->getUrl(), $product->getUrl());
            Assert::same(5, (int)$product->getQuantity());
            Assert::same($catalogProduct->getCode(), $product->getCode());
            Assert::same((float)$catalogProduct->getPrice(), (float)$product->getPrice());
            Assert::same((float)$catalogProduct->getVat(), (float)$product->getVat());
            Assert::same($expectedStock, (bool)$product->getStock());
        }
    }



    public function testAddForNoPublishedCatalogProduct()
    {
        $opportunity = $this->createTestOpportunity();
        $this->saveWithoutForeignKeysCheck($opportunity, $this->opportunityRepo);
        $this->addEntityForRemove($opportunity, $this->opportunityRepo);

        $catalogProduct = $this->createTestProduct();
        $catalogProduct->setState($catalogProduct::DRAFT);
        $this->catalogProductRepo->save($catalogProduct);
        $this->addEntityForRemove($catalogProduct, $this->catalogProductRepo);

        Assert::exception(function () use ($opportunity, $catalogProduct) {
            $this->productStorageFacade->add((int)$opportunity->getId(), (int)$catalogProduct->getId(), 1);
        }, ProductStorageFacadeException::class, 'product.not.found.general');
    }



    public function getStockLoop() : array
    {
        return [
            [1, TRUE],
            [5, TRUE],
            [0, FALSE],
        ];
    }
}

(new ProductStorageFacadeTest())->run();