<?php

declare(strict_types = 1);

namespace App\Tests\Category;

use App\Category\CategoryEntity;
use App\Category\CategoryEntityFactory;
use App\Category\CategoryNotFoundException;
use App\Category\CategoryRemoveFacadeException;
use App\Category\CategoryRemoveFacadeFactory;
use App\Category\CategoryRepositoryFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryRemoveFacadeTest extends BaseTestCase
{


    use CategoryTestTrait;



    public function testRemoveCategory()
    {
        //save a test category
        $categoryEntity = $this->createTestCategory();

        $categoryRepositoryFactory = $this->container->getByType(CategoryRepositoryFactory::class);
        $categoryRepository = $categoryRepositoryFactory->create();
        $categoryRepository->save($categoryEntity);

        $removeFacadeFactory = $this->container->getByType(CategoryRemoveFacadeFactory::class);
        $removeFacade = $removeFacadeFactory->create();
        $removeFacade->remove($categoryEntity->getId());

        Assert::exception(function () use ($categoryRepository, $categoryEntity) {
            $categoryRepository->getOneById($categoryEntity->getId());
        }, CategoryNotFoundException::class, sprintf('Kategorie s id \'%s\' nebyla nalezena.', $categoryEntity->getId()));
    }



    public function testRemoveNotExistsCategory()
    {
        $id = 55;

        $removeFacadeFactory = $this->container->getByType(CategoryRemoveFacadeFactory::class);
        $removeFacade = $removeFacadeFactory->create();

        Assert::exception(function () use ($id, $removeFacade) {
            $removeFacade->remove($id);
        }, CategoryRemoveFacadeException::class, sprintf('Kategorie s id \'%s\' nebyla nalezena.', $id));
    }
}

(new CategoryRemoveFacadeTest())->run();