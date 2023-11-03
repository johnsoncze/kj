<?php

namespace App\Tests\ProductParameter;

use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterEntityFactory;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameter\ProductParameterRepositoryFactory;
use App\ProductParameter\ProductParameterTranslationEntity;
use App\ProductParameter\ProductParameterTranslationRepositoryFactory;
use App\ProductParameter\ProductParameterTranslationSaveFacadeException;
use App\ProductParameter\ProductParameterTranslationSaveFacadeFactory;
use App\Tests\BaseTestCase;
use Nette\Database\Context;
use Tester\Assert;


require_once __DIR__ . "/../../bootstrap.php";

\Tester\Environment::lock("database", TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterTranslationAddTest extends BaseTestCase
{


    /** @var ProductParameterRepository|null */
    protected $parameterRepository;

    /** @var ProductParameterEntity|null */
    protected $parameter;

    /** @var ProductParameterTranslationEntity[]|null */
    protected $translations = [];



    protected function setUp()
    {
        parent::setUp();

        $parameterFactory = new ProductParameterEntityFactory();
        $this->parameter = $parameterFactory->create(89);

        $database = $this->container->getByType(Context::class);

        $parameterRepositoryFactory = $this->container->getByType(ProductParameterRepositoryFactory::class);
        $this->parameterRepository = $parameterRepositoryFactory->create();

        $database->query("SET FOREIGN_KEY_CHECKS = 0");
        $this->parameterRepository->save($this->parameter);
        $database->query("SET FOREIGN_KEY_CHECKS = 1");
    }



    public function testAddSuccessWithUrl()
    {
        $languageId = 1;
        $value = "Ocel";
        $url = "ocelový parameter !!:-)";
        $formattedUrl = "ocelovy-parameter";

        $saveFacadeFactory = $this->container->getByType(ProductParameterTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        $translation = $saveFacade->add($this->parameter, $languageId, $value, $url);

        //load translation from storage
        $translationRepositoryFactory = $this->container->getByType(ProductParameterTranslationRepositoryFactory::class);
        $translationRepository = $translationRepositoryFactory->create();
        $this->translations[] = $translationFromStorage = $translationRepository->getOneById($translation->getId());

        //tests if method for add a translation returns right entity
        Assert::type(ProductParameterTranslationEntity::class, $translation);
        Assert::same($this->parameter->getId(), $translation->getProductParameterId());
        Assert::same($languageId, $translation->getLanguageId());
        Assert::same($value, $translation->getValue());
        Assert::same($formattedUrl, $translation->getUrl());

        Assert::type(ProductParameterTranslationEntity::class, $translationFromStorage);
        Assert::same($this->parameter->getId(), $translationFromStorage->getProductParameterId());
        Assert::same($languageId, $translationFromStorage->getLanguageId());
        Assert::same($value, $translationFromStorage->getValue());
        Assert::same($formattedUrl, $translationFromStorage->getUrl());
    }



    public function testSaveDuplicateValue()
    {
        $languageId = 1;
        $value = "Ocel";
        $url = "ocelový parameter !!:-)";

        $saveFacadeFactory = $this->container->getByType(ProductParameterTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        $this->translations[] = $translation = $saveFacade->add($this->parameter, $languageId, $value, $url);

        Assert::exception(function () use ($saveFacade, $languageId, $value, $url) {
            $saveFacade->add($this->parameter, $languageId, $value, $url);
        }, ProductParameterTranslationSaveFacadeException::class);
    }



    public function testAddForUnknownLanguage()
    {
        $saveFacadeFactory = $this->container->getByType(ProductParameterTranslationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->add($this->parameter, 778, "Value");
        }, ProductParameterTranslationSaveFacadeException::class);
    }



    protected function tearDown()
    {
        parent::tearDown();

        //remove test translations
        if ($this->translations) {
            $translationRepositoryFactory = $this->container->getByType(ProductParameterTranslationRepositoryFactory::class);
            $translationRepository = $translationRepositoryFactory->create();
            foreach ($this->translations as $translation) {
                $translationRepository->remove($translation);
            }
        }

        //remove test parameter
        $this->parameterRepository->remove($this->parameter);
    }
}

(new ProductParameterTranslationAddTest())->run();