<?php

namespace App\Tests\ProductParameter;

use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterEntityFactory;
use App\ProductParameter\ProductParameterNotFoundException;
use App\ProductParameter\ProductParameterRemoveFacadeException;
use App\ProductParameter\ProductParameterRemoveFacadeFactory;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameter\ProductParameterRepositoryFactory;
use App\Tests\BaseTestCase;
use Nette\Database\Context;
use Tester\Assert;


require_once __DIR__ . "/../bootstrap.php";

\Tester\Environment::lock("database", TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterRemoveFacadeTest extends BaseTestCase
{


    /** @var ProductParameterRepository|null */
    protected $productParameterRepository;

    /** @var ProductParameterEntity|null */
    protected $parameter;



    protected function setUp()
    {
        parent::setUp();

        $database = $this->container->getByType(Context::class);

        //save test parameter
        $parameterFactory = new ProductParameterEntityFactory();
        $this->parameter = $parameterFactory->create(455);
        $productParameterFactory = $this->container->getByType(ProductParameterRepositoryFactory::class);
        $this->productParameterRepository = $productParameterFactory->create();

        //temporary disable checks of foreign key because group of parameter is unknown
        $database->query("SET FOREIGN_KEY_CHECKS = 0");
        $this->productParameterRepository->save($this->parameter);
        $database->query("SET FOREIGN_KEY_CHECKS = 1");
    }



    public function testRemoveSuccess()
    {
        //remove parameter from storage
        $removeFacadeFactory = $this->container->getByType(ProductParameterRemoveFacadeFactory::class);
        $removeFacade = $removeFacadeFactory->create();
        $removeFacade->remove($this->parameter->getId());

        Assert::exception(function () {
            $this->productParameterRepository->getOneById($this->parameter->getId());
        }, ProductParameterNotFoundException::class);
    }



    public function testRemoveUnknownParameter()
    {
        $removeFacadeFactory = $this->container->getByType(ProductParameterRemoveFacadeFactory::class);
        $removeFacade = $removeFacadeFactory->create();

        Assert::exception(function () use ($removeFacade) {
            $removeFacade->remove(45454578787874123);
        }, ProductParameterRemoveFacadeException::class);
    }



    protected function tearDown()
    {
        parent::tearDown();

        //remove test data
        if ($this->parameter instanceof ProductParameterEntity) {
            $this->productParameterRepository->remove($this->parameter);
        }
    }


}

(new ProductParameterRemoveFacadeTest())->run();