<?php

declare(strict_types = 1);

namespace App\Tests\Category;

use App\Category\CategoryEntity;
use App\Category\CategoryRepository;
use App\Category\CategoryRepositoryFactory;
use App\Category\CategorySaveFacadeException;
use App\Category\CategorySaveFacadeFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategorySaveFacadeTest extends BaseTestCase
{


    use CategoryTestTrait;


    /** @var string */
    const CATEGORY_TEST_NAME = 'Název testovací kategorie';
    const CATEGORY_TEST_URL = 'url-testovaci-kategorie';

    /** @var array|CategoryEntity[] */
    protected $categories = [];

    /** @var CategoryRepository|null */
    protected $categoryRepository;



    public function setUp()
    {
        parent::setUp();

        //save a test category
        $category = $this->createTestCategory();
        $category->setName(self::CATEGORY_TEST_NAME);
        $category->setUrl(self::CATEGORY_TEST_URL);

        $categoryRepositoryFactory = $this->container->getByType(CategoryRepositoryFactory::class);
        $this->categoryRepository = $categoryRepositoryFactory->create();
        $this->categoryRepository->save($category);

        //load entity from storage
        $this->categories[] = $this->categoryRepository->getOneById($category->getId());
    }



    public function testSaveNew()
    {
        $languageId = 1;
        $name = 'Kategory pro produkty';
        $content = 'Toto je obsah kategorie';
        $url = 'url-kategorie';
        $titleSeo = 'Titulek pro SEO';
        $descriptionSeo = 'Popis pro SEO';
        $sort = 20;
        $status = CategoryEntity::PUBLISH;

        /** @var $saveFacadeFactory CategorySaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(CategorySaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $this->categories[] = $category = $saveFacade->add($languageId, NULL, $name, $content, $url, $titleSeo,
            $descriptionSeo, $sort, $status);

        //load the saved category from storage
        $categoryFromStorage = $this->categoryRepository->getOneById($category->getId());

        //check if the method for add category returns right object
        Assert::same($languageId, (int)$category->getLanguageId());
        Assert::null($category->getParentCategoryId());
        Assert::same($name, $category->getName());
        Assert::same($content, $category->getContent());
        Assert::same($url, $category->getUrl());
        Assert::same($titleSeo, $category->getTitleSeo());
        Assert::same($descriptionSeo, $category->getDescriptionSeo());
        Assert::same($status, $category->getStatus());

        //check the saved category
        Assert::same($languageId, (int)$categoryFromStorage->getLanguageId());
        Assert::null($categoryFromStorage->getParentCategoryId());
        Assert::same($name, $categoryFromStorage->getName());
        Assert::same($content, $categoryFromStorage->getContent());
        Assert::same($url, $categoryFromStorage->getUrl());
        Assert::same($titleSeo, $categoryFromStorage->getTitleSeo());
        Assert::same($descriptionSeo, $categoryFromStorage->getDescriptionSeo());
        Assert::same($status, $categoryFromStorage->getStatus());
    }



    public function testSaveNewDuplicateCategory()
    {
        $category = $this->categories[0];

        /** @var $saveFacadeFactory CategorySaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(CategorySaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        //save with only duplicate name
        Assert::exception(function () use ($saveFacade, $category) {
            $saveFacade->add($category->getLanguageId(), NULL, $category->getName(), NULL,
                'category-url-new', NULL, NULL, NULL, CategoryEntity::DRAFT);
        }, CategorySaveFacadeException::class, sprintf("Kategorie s názvem '%s' již existuje.",
            $category->getName()));

        //save with duplicate name and url
        Assert::exception(function () use ($saveFacade, $category) {
            $saveFacade->add($category->getLanguageId(), NULL, $category->getName(), NULL,
                $category->getUrl(), NULL, NULL, NULL, CategoryEntity::DRAFT);
        }, CategorySaveFacadeException::class);
    }



    public function testUpdateCategory()
    {
        $name = 'A new name of category';
        $content = 'A new content of category.';
        $url = 'a-new-url-of-category';
        $titleSeo = 'A new title of seo';
        $descriptionSeo = 'A new description of seo';
        $sort = 15;
        $status = CategoryEntity::DRAFT;

        $category = $this->categories[0];
        $category->setName($name);
        $category->setContent($content);
        $category->setUrl($url);
        $category->setTitleSeo($titleSeo);
        $category->setDescriptionSeo($descriptionSeo);
        $category->setSort($sort);
        $category->setStatus($status);

        /** @var $saveFacadeFactory CategorySaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(CategorySaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $saveFacade->update($category);

        //load the saved category from storage
        $categoryFromStorage = $this->categoryRepository->getOneById($category->getId());

        Assert::same($category->getLanguageId(), (int)$categoryFromStorage->getLanguageId());
        Assert::null($categoryFromStorage->getParentCategoryId());
        Assert::same($name, $categoryFromStorage->getName());
        Assert::same($content, $categoryFromStorage->getContent());
        Assert::same($url, $categoryFromStorage->getUrl());
        Assert::same($titleSeo, $categoryFromStorage->getTitleSeo());
        Assert::same($descriptionSeo, $categoryFromStorage->getDescriptionSeo());
        Assert::same($status, $categoryFromStorage->getStatus());
    }



    public function testUpdateDuplicateCategory()
    {
        //create another test category
        $name = 'New name of category with amazing products';
        $url = 'url-of-category-with-amazing-products';
        $this->categories[] = $category = $this->createTestCategory();
        $category->setName($name);
        $category->setUrl($url);
        $this->categoryRepository->save($category);

        $categoryForUpdate = $this->categories[0];

        /** @var $saveFacadeFactory CategorySaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(CategorySaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        //update with duplicate name
        Assert::exception(function () use ($saveFacade, $category, $categoryForUpdate) {
            $cat = clone $categoryForUpdate;
            $cat->setName($category->getName());
            $saveFacade->update($cat);
        }, CategorySaveFacadeException::class, sprintf("Kategorie s názvem '%s' již existuje.",
            $category->getName()));

        //update with duplicate name and url
        Assert::exception(function () use ($saveFacade, $category, $categoryForUpdate) {
            $cat = clone $categoryForUpdate;
            $cat->setUrl($category->getUrl());
            $cat->setName($category->getName());
            $saveFacade->update($cat);
        }, CategorySaveFacadeException::class);
    }



    public function tearDown()
    {
        parent::tearDown();

        //remove test categories
        foreach ($this->categories as $category) {
            $this->categoryRepository->remove($category);
        }

        //empty categories
        $this->categories = [];
    }
}

(new CategorySaveFacadeTest())->run();