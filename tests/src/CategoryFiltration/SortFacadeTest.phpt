<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltration;

use App\Category\CategoryEntity;
use App\Category\CategoryFiltrationRepository;
use App\Category\CategoryRepository;
use App\Category\CategoryRepositoryFactory;
use App\CategoryFiltration\CategoryFiltrationEntity;
use App\CategoryFiltration\CategoryFiltrationEntityFactory;
use App\CategoryFiltration\CategoryFiltrationRepositoryFactory;
use App\CategoryFiltration\CategoryFiltrationSortFacadeFactory;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use App\ProductParameterGroup\ProductParameterGroupRepositoryFactory;
use App\Tests\BaseTestCase;
use App\Tests\Category\CategoryTestTrait;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SortFacadeTest extends BaseTestCase
{


    use CategoryTestTrait;


    /** @var ProductParameterGroupEntity[]|array */
    protected $parameterGroups = [];

    /** @var CategoryEntity|null */
    protected $category;

    /** @var CategoryFiltrationEntity[]|array */
    protected $filtrations = [];

    /** --------- Repositories --------- */

    /** @var ProductParameterGroupRepository|null */
    protected $parameterGroupRepo;

    /** @var CategoryRepository|null */
    protected $categoryRepo;

    /** @var CategoryFiltrationRepository|null */
    protected $filtrationRepo;



    public function setUp()
    {
        parent::setUp();

        //save group of parameters
        $this->parameterGroups[] = $group1 = new ProductParameterGroupEntity();
        $this->parameterGroups[] = $group2 = new ProductParameterGroupEntity();
        $this->parameterGroups[] = $group3 = new ProductParameterGroupEntity();
        $parameterGroupRepoFactory = $this->container->getByType(ProductParameterGroupRepositoryFactory::class);
        $this->parameterGroupRepo = $parameterGroupRepoFactory->create();
        $this->parameterGroupRepo->save($this->parameterGroups);

        //save test category
        $this->category = $this->createTestCategory();
        $categoryRepoFactory = $this->container->getByType(CategoryRepositoryFactory::class);
        $this->categoryRepo = $categoryRepoFactory->create();
        $this->categoryRepo->save($this->category);

        //save test filtrations
        $filtrationFactory = new CategoryFiltrationEntityFactory();
        $this->filtrations[] = $filtration1 = $filtrationFactory->create($this->category->getId(),
            $group1->getId(), TRUE, TRUE, TRUE);
        $this->filtrations[] = $filtration2 = $filtrationFactory->create($this->category->getId(),
            $group2->getId(), TRUE, TRUE, TRUE);
        $this->filtrations[] = $filtration3 = $filtrationFactory->create($this->category->getId(),
            $group3->getId(), TRUE, TRUE, TRUE);
        $filtrationRepoFactory = $this->container->getByType(CategoryFiltrationRepositoryFactory::class);
        $this->filtrationRepo = $filtrationRepoFactory->create();
        $this->filtrationRepo->save($this->filtrations);
    }



    public function testSaveSort()
    {
        $sort = [1 => $this->filtrations[1]->getId(), 2 => $this->filtrations[2]->getId(), 3 => $this->filtrations[0]->getId()];

        $sortFacadeFactory = $this->container->getByType(CategoryFiltrationSortFacadeFactory::class);
        $sortFacade = $sortFacadeFactory->create();
        $sortFacade->sort($this->category, $sort);

        //load sorted filtrations from storage
        $filtrationsFromStorage = $this->filtrationRepo->findByCategoryId($this->category->getId());

        Assert::same(array_values($sort), array_keys($filtrationsFromStorage));
    }



    public function tearDown()
    {
        parent::tearDown();

        //remove test data
        $this->filtrationRepo->remove($this->filtrations);
        $this->categoryRepo->remove($this->category);
        $this->parameterGroupRepo->remove($this->parameterGroups);
    }
}

(new SortFacadeTest())->run();