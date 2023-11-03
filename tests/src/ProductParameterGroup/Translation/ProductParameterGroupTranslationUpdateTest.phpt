<?php

declare(strict_types = 1);

namespace App\Tests\ProductParameterGroup;

use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use App\ProductParameterGroup\ProductParameterGroupRepositoryFactory;
use App\ProductParameterGroup\ProductParameterGroupTranslationEntity;
use App\ProductParameterGroup\ProductParameterGroupTranslationEntityFactory;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepository;
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
class ProductParameterGroupTranslationUpdateTest extends BaseTestCase
{


    use ProductParameterGroupTestTrait;

    /** @var ProductParameterGroupRepository|null */
    protected $productParameterGroupRepository;

    /** @var ProductParameterGroupTranslationRepository|null */
    protected $productParameterGroupTranslationRepository;

    /** @var ProductParameterGroupEntity|null */
    protected $productParameterGroupEntity;

    /** @var ProductParameterGroupEntity|null */
    protected $productParameterGroupEntity2;

    /** @var ProductParameterGroupTranslationEntity|null */
    protected $productParameterGroupTranslationEntity;

    /** @var ProductParameterGroupTranslationEntity|null */
    protected $productParameterGroupTranslationEntity2;



    public function setUp()
    {
        parent::setUp();

        //save a test group
        $productParameterGroupRepositoryFactory = $this->container->getByType(ProductParameterGroupRepositoryFactory::class);
        $this->productParameterGroupRepository = $productParameterGroupRepositoryFactory->create();
        $this->productParameterGroupEntity = $this->createTestProductParameterGroup();
        $this->productParameterGroupRepository->save($this->productParameterGroupEntity);

        //save translation of test group
        $translationEntityFactory = new ProductParameterGroupTranslationEntityFactory();
        $this->productParameterGroupTranslationEntity = $translationEntityFactory->create((int)$this->productParameterGroupEntity->getId(), 2, "Materials", "Material");
        $productParameterGroupTranslationRepositoryFactory = $this->container->getByType(ProductParameterGroupTranslationRepositoryFactory::class);
        $this->productParameterGroupTranslationRepository = $productParameterGroupTranslationRepositoryFactory->create();
        $this->productParameterGroupTranslationRepository->save($this->productParameterGroupTranslationEntity);
    }



    public function testUpdateSuccess()
    {
        $this->productParameterGroupTranslationEntity->setName("A new name of group");
        $this->productParameterGroupTranslationEntity->setFiltrationTitle("A new title");

        $saveFacadeFactory = $this->container->getByType(ProductParameterGroupTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        $saveFacade->update($this->productParameterGroupTranslationEntity);

        //load translation from storage
        $translationFromStorage = $this->productParameterGroupTranslationRepository->getOneById($this->productParameterGroupTranslationEntity->getId());

        Assert::type(ProductParameterGroupTranslationEntity::class, $translationFromStorage);
        Assert::same($this->productParameterGroupTranslationEntity->getName(), $translationFromStorage->getName());
        Assert::same($this->productParameterGroupTranslationEntity->getFiltrationTitle(), $translationFromStorage->getFiltrationTitle());
    }



    public function testUpdateWithDuplicateName()
    {
        $name = "Another name of group";
        $filtrationTitle = "Another title";

        //save another test group
        $this->productParameterGroupEntity2 = $this->createTestProductParameterGroup();
        $this->productParameterGroupRepository->save($this->productParameterGroupEntity2);

        //save translation of another test group
        $translationEntityFactory = new ProductParameterGroupTranslationEntityFactory();
        $this->productParameterGroupTranslationEntity2 = $translationEntityFactory->create((int)$this->productParameterGroupEntity2->getId(),
            $this->productParameterGroupTranslationEntity->getLanguageId(), $name, $filtrationTitle);
        $this->productParameterGroupTranslationRepository->save($this->productParameterGroupTranslationEntity2);

        //set same values as has another test group
        $this->productParameterGroupTranslationEntity->setName($name);
        $this->productParameterGroupTranslationEntity->setFiltrationTitle($filtrationTitle);

        $saveFacadeFactory = $this->container->getByType(ProductParameterGroupTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->update($this->productParameterGroupTranslationEntity);
        }, ProductParameterGroupTranslationSaveFacadeException::class);
    }



    public function tearDown()
    {
        parent::tearDown();

        //remove test data
        $this->productParameterGroupTranslationRepository->remove($this->productParameterGroupTranslationEntity);
        $this->productParameterGroupRepository->remove($this->productParameterGroupEntity);

        if ($this->productParameterGroupTranslationEntity2 instanceof ProductParameterGroupTranslationEntity) {
            $this->productParameterGroupTranslationRepository->remove($this->productParameterGroupTranslationEntity2);
        }

        if ($this->productParameterGroupEntity2 instanceof ProductParameterGroupEntity) {
            $this->productParameterGroupRepository->remove($this->productParameterGroupEntity2);
        }
    }
}

(new ProductParameterGroupTranslationUpdateTest())->run();