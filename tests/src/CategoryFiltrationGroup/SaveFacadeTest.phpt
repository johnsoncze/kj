<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltrationGroup;

use App\Category\CategoryEntity;
use App\Category\CategoryRepository;
use App\Category\CategoryRepositoryFactory;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepositoryFactory;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupSaveFacade;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupSaveFacadeException;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupSaveFacadeFactory;
use App\Tests\BaseTestCase;
use App\Tests\Category\CategoryTestTrait;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SaveFacadeTest extends BaseTestCase
{

    use CategoryFiltrationGroupTestTrait;
    use CategoryTestTrait;

    /** @var CategoryEntity|null */
    protected $category;

    /** @var CategoryFiltrationGroupEntity|null */
    protected $group;

    /** @var CategoryFiltrationGroupRepository|null */
    protected $groupRepo;

    /** @var CategoryRepository|null */
    protected $categoryRepo;



    public function setUp()
    {
        parent::setUp();

        //save a test category
        $this->category = $this->createTestCategory();
        $categoryRepoFactory = $this->container->getByType(CategoryRepositoryFactory::class);
        $this->categoryRepo = $categoryRepoFactory->create();
        $this->categoryRepo->save($this->category);

        $groupRepoFactory = $this->container->getByType(CategoryFiltrationGroupRepositoryFactory::class);
        $this->groupRepo = $groupRepoFactory->create();
    }



    public function testSaveNewGroup()
    {
        $description = 'This is a description.';
        $titleSeo = 'Title for SEO';
        $descriptionSeo = 'Description for SEO';
        $index = FALSE;
        $follow = FALSE;

        /** @var $saveFacadeFactory CategoryFiltrationGroupSaveFacade */
        $saveFacadeFactory = $this->container->getByType(CategoryFiltrationGroupSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        $this->group = $saveFacade->add($this->category, $description, $titleSeo, $descriptionSeo,
            $index, $follow, CategoryFiltrationGroupEntity::PUBLISH);

        //load saved group storage
        $groupFromStorage = $this->groupRepo->getOneById($this->group->getId());

        //check entity which returns method for add a new group
        Assert::type(CategoryFiltrationGroupEntity::class, $this->group);
        Assert::same($description, $this->group->getDescription());
        Assert::same($titleSeo, $this->group->getTitleSeo());
        Assert::same($descriptionSeo, $this->group->getDescriptionSeo());
        Assert::false((bool)$this->group->getIndexSeo());
        Assert::false((bool)$this->group->getSiteMap());
        Assert::false((bool)$this->group->getFollowSeo());

        Assert::type(CategoryFiltrationGroupEntity::class, $groupFromStorage);
        Assert::same($description, $groupFromStorage->getDescription());
        Assert::same($titleSeo, $groupFromStorage->getTitleSeo());
        Assert::same($descriptionSeo, $groupFromStorage->getDescriptionSeo());
        Assert::false((bool)$groupFromStorage->getIndexSeo());
        Assert::false((bool)$groupFromStorage->getSiteMap());
        Assert::false((bool)$groupFromStorage->getFollowSeo());
    }



    public function testSaveNewWithNotExistsCategory()
    {
        $id = $this->category->getId() + 100;

        /** @var $saveFacade CategoryFiltrationGroupSaveFacade */
        $saveFacadeFactory = $this->container->getByType(CategoryFiltrationGroupSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade, $id) {
            $category = clone $this->category;
            $category->setId($id);
            $saveFacade->add($category, NULL, NULL, NULL, TRUE, TRUE, CategoryEntity::PUBLISH);
        }, CategoryFiltrationGroupSaveFacadeException::class, sprintf("Kategorie s id '%s' nebyla nalezena.", $id));
    }



    public function testUpdate()
    {
        //save a test group
        $this->group = $this->createTestGroup();
        $this->group->setCategoryId($this->category->getId());
        $this->groupRepo->save($this->group);

        //update
        $this->group->setDescription('A new description');
        $this->group->setTitleSeo('A new title for SEO');
        $this->group->setDescriptionSeo('A new description for SEO');
        $this->group->setIndexSeo(TRUE);
        $this->group->setFollowSeo(TRUE);

        /** @var $saveFacadeFactory CategoryFiltrationGroupSaveFacade */
        $saveFacadeFactory = $this->container->getByType(CategoryFiltrationGroupSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $saveFacade->update($this->group);

        //load updated group from storage
        $groupFromStorage = $this->groupRepo->getOneById($this->group->getId());

        Assert::type(CategoryFiltrationGroupEntity::class, $groupFromStorage);
        Assert::same($this->group->getDescription(), $groupFromStorage->getDescription());
        Assert::same($this->group->getTitleSeo(), $groupFromStorage->getTitleSeo());
        Assert::same($this->group->getDescriptionSeo(), $groupFromStorage->getDescriptionSeo());
        Assert::true((bool)$groupFromStorage->getIndexSeo());
        Assert::true((bool)$groupFromStorage->getSiteMap());
        Assert::true((bool)$groupFromStorage->getFollowSeo());
    }



    public function tearDown()
    {
        parent::tearDown();

        //remove the test category
        $this->categoryRepo->remove($this->category);

        //remove the test group
        $this->groupRepo->remove($this->group);
    }
}

(new SaveFacadeTest())->run();