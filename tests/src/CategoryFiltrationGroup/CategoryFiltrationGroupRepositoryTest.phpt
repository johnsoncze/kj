<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltrationGroup;

use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterEntity;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterRepository;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CategoryFiltrationGroupRepositoryTest extends BaseTestCase
{

    use CategoryFiltrationGroupTestTrait;

    /* @var CategoryFiltrationGroupRepository */
    private $categoryFiltrationGroupRepo;

    /** @var CategoryFiltrationGroupParameterRepository */
    private $categoryFiltrationGroupParameterRepo;



    protected function setUp()
    {
        parent::setUp();
        $this->categoryFiltrationGroupRepo = $this->container->getByType(CategoryFiltrationGroupRepository::class);
        $this->categoryFiltrationGroupParameterRepo = $this->container->getByType(CategoryFiltrationGroupParameterRepository::class);
    }



    public function testFindOneByCategoryIdAndMoreParameterId()
    {
        //prepare test group
        $group = $this->createTestGroup();
        $this->saveWithoutForeignKeysCheck($group, $this->categoryFiltrationGroupRepo);
        $this->addEntityForRemove($group, $this->categoryFiltrationGroupRepo);

        //prepare test group parameters
        $parameter1 = new CategoryFiltrationGroupParameterEntity();
        $parameter1->setCategoryFiltrationGroupId($group->getId());
        $parameter1->setProductParameterId(1);
        $parameter2 = new CategoryFiltrationGroupParameterEntity();
        $parameter2->setCategoryFiltrationGroupId($group->getId());
        $parameter2->setProductParameterId(2);
        $parameter3 = new CategoryFiltrationGroupParameterEntity();
        $parameter3->setCategoryFiltrationGroupId($group->getId());
        $parameter3->setProductParameterId(3);
        $this->saveWithoutForeignKeysCheck($parameter1, $this->categoryFiltrationGroupParameterRepo);
        $this->saveWithoutForeignKeysCheck($parameter2, $this->categoryFiltrationGroupParameterRepo);
        $this->saveWithoutForeignKeysCheck($parameter3, $this->categoryFiltrationGroupParameterRepo);

        //another category
        Assert::null($this->categoryFiltrationGroupRepo->findOneByCategoryIdAndMoreParameterId(2, [$parameter1->getProductParameterId(), $parameter2->getProductParameterId(), $parameter3->getProductParameterId()]));

        //not all or another parameters
        Assert::null($this->categoryFiltrationGroupRepo->findOneByCategoryIdAndMoreParameterId($group->getCategoryId(), [$parameter1->getProductParameterId(), $parameter2->getProductParameterId()]));
        Assert::null($this->categoryFiltrationGroupRepo->findOneByCategoryIdAndMoreParameterId($group->getCategoryId(), [$parameter1->getProductParameterId()]));
        Assert::null($this->categoryFiltrationGroupRepo->findOneByCategoryIdAndMoreParameterId($group->getCategoryId(), [$parameter1->getProductParameterId(), $parameter2->getProductParameterId(), 97]));

        //success
        Assert::same($group->getId(), (int)$this->categoryFiltrationGroupRepo->findOneByCategoryIdAndMoreParameterId($group->getCategoryId(), [$parameter1->getProductParameterId(), $parameter2->getProductParameterId(), $parameter3->getProductParameterId()])->getId());
    }
}

(new CategoryFiltrationGroupRepositoryTest())->run();