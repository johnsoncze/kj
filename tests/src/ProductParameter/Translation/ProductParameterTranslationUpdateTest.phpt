<?php

namespace App\Tests\ProductParameter;

use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterEntityFactory;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameter\ProductParameterRepositoryFactory;
use App\ProductParameter\ProductParameterTranslationEntity;
use App\ProductParameter\ProductParameterTranslationEntityFactory;
use App\ProductParameter\ProductParameterTranslationRepository;
use App\ProductParameter\ProductParameterTranslationRepositoryFactory;
use App\ProductParameter\ProductParameterTranslationSaveFacadeException;
use App\ProductParameter\ProductParameterTranslationSaveFacadeFactory;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use App\ProductParameterGroup\ProductParameterGroupRepositoryFactory;
use App\Tests\BaseTestCase;
use App\Tests\ProductParameterGroup\ProductParameterGroupTestTrait;
use Tester\Assert;


require_once __DIR__ . "/../../bootstrap.php";

\Tester\Environment::lock("database", TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterTranslationUpdateTest extends BaseTestCase
{


    use ProductParameterGroupTestTrait;


    /** ---------- Repositories ---------- */

    /** @var ProductParameterGroupRepository|null */
    protected $groupRepository;

    /** @var ProductParameterRepository|null */
    protected $parameterRepository;

    /** @var ProductParameterTranslationRepository|null */
    protected $parameterTranslationRepository;

    /** ---------- Entities ---------- */

    /** @var ProductParameterGroupEntity|null */
    protected $groupEntity;

    /** @var ProductParameterEntity|null */
    protected $parameterEntity;

    /** @var ProductParameterEntity|null */
    protected $parameterEntity2;

    /** @var ProductParameterTranslationEntity|null */
    protected $parameterTranslationEntity;

    /** @var ProductParameterTranslationEntity|null */
    protected $parameterTranslationEntity2;



    protected function setUp()
    {
        parent::setUp();

        //save test group of parameters
        $this->groupEntity = $groupEntity = $this->createTestProductParameterGroup();
        $groupRepositoryFactory = $this->container->getByType(ProductParameterGroupRepositoryFactory::class);
        $this->groupRepository = $groupRepository = $groupRepositoryFactory->create();
        $groupRepository->save($groupEntity);

        //save test parameter
        $parameterEntityFactory = new ProductParameterEntityFactory();
        $this->parameterEntity = $parameterEntity = $parameterEntityFactory->create((int)$groupEntity->getId());
        $parameterRepositoryFactory = $this->container->getByType(ProductParameterRepositoryFactory::class);
        $this->parameterRepository = $parameterRepository = $parameterRepositoryFactory->create();
        $parameterRepository->save($parameterEntity);

        //save translation of parameter
        $parameterTranslationEntityFactory = new ProductParameterTranslationEntityFactory();
        $parameterTranslationEntity = $parameterTranslationEntityFactory->create((int)$parameterEntity->getId(), 1, "Ocel", "ocel");
        $parameterTranslationRepositoryFactory = $this->container->getByType(ProductParameterTranslationRepositoryFactory::class);
        $this->parameterTranslationRepository = $parameterTranslationRepository = $parameterTranslationRepositoryFactory->create();
        $parameterTranslationRepository->save($parameterTranslationEntity);
        $this->parameterTranslationEntity = $parameterTranslationRepository->getOneById($parameterTranslationEntity->getId());
        //load full parameter translation from storage
    }



    public function testUpdateSuccess()
    {
        //update entity
        $value = "Nový název";
        $url = "nova-url";
        $this->parameterTranslationEntity->setValue($value);
        $this->parameterTranslationEntity->setUrl($url);

        //save updated entity
        $saveFacadeFactory = $this->container->getByType(ProductParameterTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $saveFacade->update($this->parameterEntity, $this->parameterTranslationEntity);

        //load translation from storage
        $translationFromStorage = $this->parameterTranslationRepository->getOneById($this->parameterTranslationEntity->getId());

        Assert::type(ProductParameterTranslationEntity::class, $translationFromStorage);
        Assert::same($value, $translationFromStorage->getValue());
        Assert::same($url, $translationFromStorage->getUrl());
        Assert::same($this->parameterTranslationEntity->getProductParameterId(), $translationFromStorage->getProductParameterId());
        Assert::same($this->parameterTranslationEntity->getLanguageId(), $translationFromStorage->getLanguageId());
        Assert::same($this->parameterTranslationEntity->getAddDate(), $translationFromStorage->getAddDate());
    }



    public function testUpdateWithDuplicateValue()
    {
        //save a new parameter and its translation
        $value = "Dřevo";
        $parameterEntityFactory = new ProductParameterEntityFactory();
        $this->parameterEntity2 = $parameter2 = $parameterEntityFactory->create((int)$this->groupEntity->getId());
        $this->parameterRepository->save($parameter2);
        $parameterTranslationEntityFactory = new ProductParameterTranslationEntityFactory();
        $this->parameterTranslationEntity2 = $parameterTranslation2 = $parameterTranslationEntityFactory->create((int)$parameter2->getId(), 1, $value, "drevo");
        $this->parameterTranslationRepository->save($parameterTranslation2);

        //update entity with duplicate value
        $this->parameterTranslationEntity->setValue($value);

        Assert::exception(function () {
            $saveFacadeFactory = $this->container->getByType(ProductParameterTranslationSaveFacadeFactory::class);
            $saveFacade = $saveFacadeFactory->create();
            $saveFacade->update($this->parameterEntity, $this->parameterTranslationEntity);
        }, ProductParameterTranslationSaveFacadeException::class, "Parametr '{$value}' již existuje.");
    }



    protected function tearDown()
    {
        parent::tearDown();

        //remove test data
        if ($this->parameterTranslationEntity2 instanceof ProductParameterTranslationEntity) {
            $this->parameterTranslationRepository->remove($this->parameterTranslationEntity2);
        }
        if ($this->parameterEntity2 instanceof ProductParameterEntity) {
            $this->parameterRepository->remove($this->parameterEntity2);
        }

        $this->parameterTranslationRepository->remove($this->parameterTranslationEntity);
        $this->parameterRepository->remove($this->parameterEntity);
        $this->groupRepository->remove($this->groupEntity);
    }
}

(new ProductParameterTranslationUpdateTest())->run();