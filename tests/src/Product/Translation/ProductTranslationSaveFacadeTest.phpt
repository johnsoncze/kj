<?php

declare(strict_types = 1);

namespace App\Tests\Product\Translation;

use App\Product\Product;
use App\Product\ProductFactory;
use App\Product\ProductRepository;
use App\Product\ProductRepositoryFactory;
use App\Product\Translation\ProductTranslation;
use App\Product\Translation\ProductTranslationRepository;
use App\Product\Translation\ProductTranslationRepositoryFactory;
use App\Product\Translation\ProductTranslationSaveFacadeException;
use App\Product\Translation\ProductTranslationSaveFacadeFactory;
use App\Tests\BaseTestCase;
use App\Tests\Product\ProductTestTrait;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductTranslationSaveFacadeTest extends BaseTestCase
{


    use ProductTestTrait;
    use ProductTranslationTestTrait;

    /** @var array|Product[] */
    protected $products = [];

    /** @var array|ProductTranslation[] */
    protected $translations = [];

    /** @var null|ProductRepository */
    protected $productRepo;

    /** @var null|ProductTranslationRepository */
    protected $productTranslationRepo;



    public function setUp()
    {
        parent::setUp();

        //save test products
        $productFactory = new ProductFactory();
        $this->products[] = $product = $productFactory->create('znh77-778', NULL, 1, 1, 57, 55.55, 21, Product::PUBLISH, TRUE, TRUE)->setExternalSystemId(1);
        $this->products[] = $this->createTestProduct()->setExternalSystemId(55);

        $productRepoFactory = $this->container->getByType(ProductRepositoryFactory::class);
        $this->productRepo = $productRepoFactory->create();
        $this->productRepo->save($this->products);

        //save a test translation
        $this->translations[] = $translation = $this->createTestProductTranslation();
        $translation->setProductId($product->getId());
        $translationRepoFactory = $this->container->getByType(ProductTranslationRepositoryFactory::class);
        $this->productTranslationRepo = $translationRepoFactory->create();
        $this->productTranslationRepo->save($translation);
    }



    public function testSaveNew()
    {
        $productId = (int)end($this->products)->getId();
        $languageId = 1;
        $name = 'Name of product';
        $description = 'This is a test description';
        $url = 'url   address ---- ____ %%% ###+++ 1';
        $titleSeo = 'Title of seo';
        $descriptionSeo = 'Description od seo';

        /** @var $saveFacadeFactory ProductTranslationSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ProductTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $translation = $saveFacade->saveNew($productId, $languageId, $name, $description, $url, $titleSeo, $descriptionSeo);

        //load the translation from storage
        $translationFromStorage = $this->productTranslationRepo->getOneById((int)$translation->getId());

        Assert::same($productId, $translation->getProductId());
        Assert::same($languageId, $translation->getLanguageId());
        Assert::same($name, $translation->getName());
        Assert::same($description, $translation->getDescription());
        Assert::same('url-address-1', $translation->getUrl());
        Assert::same($titleSeo, $translation->getTitleSeo());
        Assert::same($descriptionSeo, $translation->getDescriptionSeo());

        Assert::same($productId, (int)$translationFromStorage->getProductId());
        Assert::same($languageId, (int)$translationFromStorage->getLanguageId());
        Assert::same($name, $translationFromStorage->getName());
        Assert::same($description, $translationFromStorage->getDescription());
        Assert::same('url-address-1', $translationFromStorage->getUrl());
        Assert::same($titleSeo, $translationFromStorage->getTitleSeo());
        Assert::same($descriptionSeo, $translationFromStorage->getDescriptionSeo());
    }



    public function testSaveNewForUnknownProduct()
    {
        $productId = (int)(end($this->products)->getId() + 10);

        /** @var $saveFacadeFactory ProductTranslationSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ProductTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade, $productId) {
            $saveFacade->saveNew($productId, 1, 'Name of product', 'This is a test description',
                'url   address ---- ____ %%% ###+++', 'Title of seo', 'Description od seo');
        }, ProductTranslationSaveFacadeException::class, sprintf('Produkt s id "%d" nebyl nalezen.', $productId));
    }



    public function testSaveNewWithBadLanguageId()
    {
        $languageId = 19;

        /** @var $saveFacadeFactory ProductTranslationSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ProductTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade, $languageId) {
            $saveFacade->saveNew((int)end($this->products)->getId(), $languageId, 'Name of product', 'This is a test description',
                'url   address ---- ____ %%% ###+++', 'Title of seo', 'Description od seo');
        }, ProductTranslationSaveFacadeException::class, sprintf("Jazyk s id '%d' nebyl nalezen.", $languageId));
    }



    public function testUpdate()
    {
        $translationForUpdate = end($this->translations);

        $name = 'New name of product';
        $description = 'This is a new test description';
        $url = 'new url address &&&***{{}}}//   __--';
        $expectUrl = 'new-url-address';
        $titleSeo = 'New title of seo';
        $descriptionSeo = 'New description od seo';

        /** @var $saveFacadeFactory ProductTranslationSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ProductTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $translation = $saveFacade->update($translationForUpdate->getId(), $name, $description, $url, $titleSeo, $descriptionSeo);

        //load the translation from storage
        $translationFromStorage = $this->productTranslationRepo->getOneById((int)$translation->getId());

        Assert::same($translationForUpdate->getProductId(), (int)$translation->getProductId());
        Assert::same($translationForUpdate->getLanguageId(), (int)$translation->getLanguageId());
        Assert::same($name, $translation->getName());
        Assert::same($description, $translation->getDescription());
        Assert::same($expectUrl, $translation->getUrl());
        Assert::same($titleSeo, $translation->getTitleSeo());
        Assert::same($descriptionSeo, $translation->getDescriptionSeo());

        Assert::same($translationForUpdate->getProductId(), (int)$translationFromStorage->getProductId());
        Assert::same($translationForUpdate->getLanguageId(), (int)$translationFromStorage->getLanguageId());
        Assert::same($name, $translationFromStorage->getName());
        Assert::same($description, $translationFromStorage->getDescription());
        Assert::same($expectUrl, $translationFromStorage->getUrl());
        Assert::same($titleSeo, $translationFromStorage->getTitleSeo());
        Assert::same($descriptionSeo, $translationFromStorage->getDescriptionSeo());
    }



    public function testUpdateForUnknownTranslation()
    {
        $translationId = (int)end($this->translations)->getId() + 1;

        /** @var $saveFacadeFactory ProductTranslationSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ProductTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade, $translationId) {
            $saveFacade->update($translationId, 'New name of product', 'New description of product', 'url', 'Title seo', 'Description seo');
        }, ProductTranslationSaveFacadeException::class, sprintf('Překlad produktu s id "%d" nebyl nalezen.', $translationId));
    }



    public function updateForUnknownTranslation()
    {
        $translationId = (int)end($this->translations)->getId() + 1;

        /** @var $saveFacadeFactory ProductTranslationSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ProductTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade, $translationId) {
            $saveFacade->update($translationId, 'Name of product', 'This is a test description',
                'url   address ---- ____ %%% ###+++', 'Title of seo', 'Description od seo');
        }, ProductTranslationSaveFacadeException::class, sprintf('Překlad produktu s id "%d" nebyl nalezen.', $translationId));
    }



    public function tearDown()
    {
        parent::tearDown();

        //remove test products
        foreach ($this->products as $product) {
            $this->productRepo->remove($product);
        }
        $this->products = [];

        //translations are deleted by relation in storage
        $this->translations = [];
    }
}

(new ProductTranslationSaveFacadeTest())->run();