<?php

namespace App\Tests\ProductParameterGroup;

use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use App\ProductParameterGroup\ProductParameterGroupRepositoryFactory;
use App\ProductParameterGroup\ProductParameterGroupSaveFacade;
use App\ProductParameterGroup\ProductParameterGroupSaveFacadeFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../bootstrap.php";

\Tester\Environment::lock("database", TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupSaveFacadeTest extends BaseTestCase
{

    /** @var ProductParameterGroupRepository|null */
    protected $groupRepo;

    /** @var ProductParameterGroupEntity|null */
    protected $productParameterGroupEntity;



    protected function setUp()
    {
        parent::setUp();
        $this->groupRepo = $this->container->getByType(ProductParameterGroupRepository::class);
    }



    public function testSaveNewSuccess()
    {
        /** @var $saveFacade ProductParameterGroupSaveFacade */
        $saveFacade = $this->container->getByType(ProductParameterGroupSaveFacadeFactory::class)->create();
        $this->productParameterGroupEntity = $group = $saveFacade->save(NULL, ProductParameterGroupEntity::VARIANT_TYPE_IMAGE, ProductParameterGroupEntity::FILTRATION_TYPE_COLOR_LIST, 'Help text.');
        $groupFromStorage = $this->groupRepo->getOneById($group->getId());

        foreach ([$group, $groupFromStorage] as $_group) {
            Assert::type(ProductParameterGroupEntity::class, $group);
            Assert::same(ProductParameterGroupEntity::VARIANT_TYPE_IMAGE, $_group->getVariantType());
            Assert::same(ProductParameterGroupEntity::FILTRATION_TYPE_COLOR_LIST, $_group->getFiltrationType());
        }
    }



    public function tearDown()
    {
        parent::tearDown();

        //remove created group
        if ($this->productParameterGroupEntity instanceof ProductParameterGroupEntity) {
            $groupRepository = $this->container->getByType(ProductParameterGroupRepositoryFactory::class)->create();
            $groupRepository->remove($this->productParameterGroupEntity);
        }
    }
}

(new ProductParameterGroupSaveFacadeTest())->run();