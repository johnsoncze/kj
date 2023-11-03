<?php
//
//declare(strict_types = 1);
//
//namespace App\Tests\Product\Parameter;
//
//use App\Helpers\Entities;
//use App\Product\Product;
//use App\Product\ProductRepository;
//use App\Product\ProductRepositoryFactory;
//use App\ProductParameter\ProductParameterEntity;
//use App\ProductParameter\ProductParameterEntityFactory;
//use App\ProductParameter\ProductParameterRepository;
//use App\ProductParameter\ProductParameterRepositoryFactory;
//use App\ProductParameter\ProductParameterSaveFacadeFactory;
//use App\ProductParameterGroup\ProductParameterGroupEntity;
//use App\Tests\BaseTestCase;
//use App\Tests\Product\ProductTestTrait;
//use Nette\Database\Context;
//use Tester\Assert;
//
//
//require_once __DIR__ . '/../../bootstrap.php';
//
//\Tester\Environment::lock('database', TEMP_TEST);
//
///**
// * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
// */
//class ProductParameterSaveFacadeTest extends BaseTestCase
//{
//
//
//    use ProductTestTrait;
//
//    /** @var null|ProductRepository */
//    protected $productRepo;
//
//    /** @var null|ProductParameterRepository */
//    protected $parameterRepo;
//
//    /** @var null|Product */
//    protected $product;
//
//    /** @var array|ProductParameterEntity[] */
//    protected $parameters = [];
//
//
//
//    public function setUp()
//    {
//        parent::setUp();
//
//        $database = $this->container->getByType(Context::class);
//
//        //save a test product
//        $this->product = $product = $this->createTestProduct();
//
//        $productRepoFactory = $this->container->getByType(ProductRepositoryFactory::class);
//        $this->productRepo = $productRepoFactory->create();
//        $this->productRepo->save($product);
//
//        //save test parameters
//        $parameterGroupId = 1; //a group
//        $parameterFactory = new ProductParameterEntityFactory();
//        $this->parameters[] = $parameterFactory->create($parameterGroupId);
//        $this->parameters[] = $parameterFactory->create($parameterGroupId);
//        $this->parameters[] = $parameterFactory->create($parameterGroupId);
//
//        $parameterRepoFactory = $this->container->getByType(ProductParameterRepositoryFactory::class);
//        $this->parameterRepo = $parameterRepoFactory->create();
//
//        $database->query('SET foreign_key_checks = 0');
//        $this->parameterRepo->save($this->parameters);
//        $database->query('SET foreign_key_checks = 1');
//    }
//
//
//
//    public function testSaveNew()
//    {
//        $parametersId = [
//            (int)$this->parameters[1]->getId(),
//            (int)$this->parameters[2]->getId()
//        ];
//
//        /** @var $saveFacadeFactory ProductParameterSaveFacadeFactory */
//        $saveFacadeFactory = $this->container->getByType(ProductParameterSaveFacadeFactory::class);
//        $saveFacade = $saveFacadeFactory->create();
//        $group = new ProductParameterGroupEntity();
//        $saveFacade->save($this->product, $parametersId);
//
//        //load product parameters from storage
//        $productParameterRepoFactory = $this->container->getByType(\App\Product\Parameter\ProductParameterRepositoryFactory::class);
//        $productParameterRepo = $productParameterRepoFactory->create();
//        $productParameters = $productParameterRepo->findByProductId((int)$this->product->getId());
//        $productParametersId = Entities::getProperty($productParameters, 'parameterId');
//
//        Assert::count(count($parametersId), $productParameters);
//        Assert::falsey(array_diff($parametersId, $productParametersId));
//        Assert::falsey(array_diff($productParametersId, $parametersId));
//    }
//
//
//
//    public function testUpdate()
//    {
//        $primaryParametersId = [
//            (int)$this->parameters[1]->getId(),
//            (int)$this->parameters[2]->getId()
//        ];
//        $parametersIdForUpdate = [
//            (int)$this->parameters[0]->getId(),
//            (int)$this->parameters[1]->getId()
//        ];
//
//        /** @var $saveFacadeFactory ProductParameterSaveFacadeFactory */
//        $saveFacadeFactory = $this->container->getByType(ProductParameterSaveFacadeFactory::class);
//        $saveFacade = $saveFacadeFactory->create();
//        $saveFacade->save($this->product, $primaryParametersId);
//        $saveFacade->save($this->product, $parametersIdForUpdate); //update
//
//        //load product parameters from storage
//        $productParameterRepoFactory = $this->container->getByType(\App\Product\Parameter\ProductParameterRepositoryFactory::class);
//        $productParameterRepo = $productParameterRepoFactory->create();
//        $productParameters = $productParameterRepo->findByProductId((int)$this->product->getId());
//        $productParametersId = Entities::getProperty($productParameters, 'parameterId');
//
//        Assert::count(count($parametersIdForUpdate), $productParameters);
//        Assert::falsey(array_diff($parametersIdForUpdate, $productParametersId));
//        Assert::falsey(array_diff($productParametersId, $parametersIdForUpdate));
//    }
//
//
//
//    public function tearDown()
//    {
//        parent::tearDown();
//
//        //delete the test product
//        $this->productRepo->remove($this->product);
//        $this->product = NULL;
//
//        //detete test parameters
//        foreach ($this->parameters as $parameter) {
//            $this->productRepo->remove($parameter);
//        }
//        $this->parameters = [];
//    }
//}
//
//(new ProductParameterSaveFacadeTest())->run();