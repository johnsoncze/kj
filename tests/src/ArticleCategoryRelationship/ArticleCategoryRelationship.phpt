<?php

namespace App\Tests\ArticleCategoryRelationship;

require_once __DIR__ . "/../bootstrap.php";

use App\Article\ArticleEntity;
use App\ArticleCategoryRelationship\ArticleCategoryRelationshipEntity;
use App\ArticleCategoryRelationship\ArticleCategoryRelationshipFacade;
use App\ArticleCategoryRelationship\ArticleCategoryRelationshipFacadeFactory;
use App\ArticleCategoryRelationship\ArticleCategoryRelationshipRepository;
use App\ArticleCategoryRelationship\ArticleCategoryRelationshipRepositoryFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


\Tester\Environment::lock("database", TEMP_TEST);


class ArticleCategoryRelationship extends BaseTestCase
{


    /** @var ArticleCategoryRelationshipFacade */
    protected $facade;

    /** @var ArticleCategoryRelationshipRepository */
    protected $repository;

    /** @var ArticleCategoryRelationshipEntity[] */
    protected $entities;



    public function setUp()
    {
        parent::setUp();
        $this->facade = $this->container->getByType(ArticleCategoryRelationshipFacadeFactory::class)->create();
        $this->repository = $this->container->getByType(ArticleCategoryRelationshipRepositoryFactory::class)->create();

        $article = new ArticleEntity();
        $article->setId("1");

        $this->entities = $this->facade->add($article, [1, 2]);
    }



    public function testLoad()
    {
        $entity1 = $this->repository->getOneById($this->entities[0]->getId());
        $entity2 = $this->repository->getOneById($this->entities[1]->getId());

        Assert::equal($this->entities[0]->getArticleCategoryId(), $entity1->getArticleCategoryId());
        Assert::equal($this->entities[0]->getArticleId(), $entity1->getArticleId());

        Assert::equal($this->entities[1]->getArticleCategoryId(), $entity2->getArticleCategoryId());
        Assert::equal($this->entities[1]->getArticleId(), $entity2->getArticleId());
    }



    public function tearDown()
    {
        $this->repository->remove($this->entities);
    }
}

(new ArticleCategoryRelationship())->run();