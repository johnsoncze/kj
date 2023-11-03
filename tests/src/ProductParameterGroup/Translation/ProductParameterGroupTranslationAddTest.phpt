<?php

namespace App\Tests\ProductParameterGroup;

use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use App\ProductParameterGroup\ProductParameterGroupRepositoryFactory;
use App\ProductParameterGroup\ProductParameterGroupTranslationEntity;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepositoryFactory;
use App\ProductParameterGroup\ProductParameterGroupTranslationSaveFacadeException;
use App\ProductParameterGroup\ProductParameterGroupTranslationSaveFacadeFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../../bootstrap.php";

\Tester\Environment::lock("database", TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupTranslationAddTest extends BaseTestCase
{


    use ProductParameterGroupTestTrait;


    /** @var ProductParameterGroupRepository|null */
    protected $productParameterGroupRepository;

    /** @var ProductParameterGroupEntity|null */
    protected $productParameterGroupEntity;

    /** @var ProductParameterGroupTranslationEntity|null */
    protected $productParameterGroupTranslationEntity;



    public function setUp()
    {
        parent::setUp();

        //save a test group
        $this->productParameterGroupEntity = $this->createTestProductParameterGroup();
        $productParameterGroupRepositoryFactory = $this->container->getByType(ProductParameterGroupRepositoryFactory::class);
        $this->productParameterGroupRepository = $productParameterGroupRepositoryFactory->create();
        $this->productParameterGroupRepository->save($this->productParameterGroupEntity);
    }



    public function testAddNewSuccess()
    {
        $languageId = 2;
        $name = "Materials";
        $filtrationTitle = "Material";

        $saveFacadeFactory = $this->container->getByType(ProductParameterGroupTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $translation = $saveFacade->add($this->productParameterGroupEntity, $languageId, $name, $filtrationTitle);

        //load saved entity from storage
        $translationRepositoryFactory = $this->container->getByType(ProductParameterGroupTranslationRepositoryFactory::class);
        $translationRepository = $translationRepositoryFactory->create();
        $translationFromStorage = $translationRepository->getOneById($translation->getId());

        //set into class for remove later
        $this->productParameterGroupTranslationEntity = $translationFromStorage;

        //tests if method for save returns entity
        Assert::type(ProductParameterGroupTranslationEntity::class, $translationFromStorage);
        Assert::same($languageId, $translation->getLanguageId());
        Assert::same($name, $translation->getName());
        Assert::same($filtrationTitle, $translation->getFiltrationTitle());

        //tests for translation from storage
        Assert::type(ProductParameterGroupTranslationEntity::class, $translationFromStorage);
        Assert::same($languageId, $translationFromStorage->getLanguageId());
        Assert::same($name, $translationFromStorage->getName());
        Assert::same($filtrationTitle, $translationFromStorage->getFiltrationTitle());
    }



    public function testAddWithUnknownLanguage()
    {
        $saveFacadeFactory = $this->container->getByType(ProductParameterGroupTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade) {
            $languageId = 278887456;
            $name = "Materials";
            $filtrationTitle = "Material";

            $saveFacade->add($this->productParameterGroupEntity, $languageId, $name, $filtrationTitle);
        }, ProductParameterGroupTranslationSaveFacadeException::class);
    }



    public function testAddDuplicateName()
    {
        $languageId = 1;
        $name = "Materiál produktů";
        $filtrationTitle = "Materiál";

        $saveFacadeFactory = $this->container->getByType(ProductParameterGroupTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $saveFacade->add($this->productParameterGroupEntity, $languageId, $name, $filtrationTitle);

        Assert::exception(function () use ($saveFacade, $languageId, $name, $filtrationTitle) {
            $saveFacade->add($this->productParameterGroupEntity, $languageId, $name, $filtrationTitle);
        }, ProductParameterGroupTranslationSaveFacadeException::class);
    }



    public function tearDown()
    {
        parent::tearDown();

        //remove translation
        if ($this->productParameterGroupTranslationEntity instanceof ProductParameterGroupTranslationEntity) {
            $translationRepositoryFactory = $this->container->getByType(ProductParameterGroupTranslationRepositoryFactory::class);
            $translationRepository = $translationRepositoryFactory->create();
            $translationRepository->remove($this->productParameterGroupTranslationEntity);
        }

        //remove test group
        $this->productParameterGroupRepository->remove($this->productParameterGroupEntity);
    }
}

(new ProductParameterGroupTranslationAddTest())->run();