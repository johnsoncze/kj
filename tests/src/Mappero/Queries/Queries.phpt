<?php

namespace App\Tests\Mappero\Queries;

require_once __DIR__ . "/../../bootstrap.php";

\Tester\Environment::lock("database", TEMP_TEST);

use App\Article\ArticleEntity;
use App\Tests\BaseTestCase;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Queries extends BaseTestCase
{


    /** @var ArticleEntity|null */
    protected $article;

    /** @var ArticleEntity|null */
    protected $article2;



    public function setUp()
    {
        parent::setUp();

        $article = new ArticleEntity();
        $article->setLanguageId(1);
        $article->setName("Článek pro test Mappero");
        $article->setUrl("url-mappero-1");
        $article->setIntroduction("Popis článku.");
        $article->setContent("Obsah článku.");
        $article->setStatus(ArticleEntity::DRAFT);

        $article2 = new ArticleEntity();
        $article2->setLanguageId(1);
        $article2->setName("Další článek pro test Mappero");
        $article2->setUrl("url-mappero-2");
        $article2->setIntroduction("Popis článku. 2");
        $article2->setContent("Obsah článku. 2222");
        $article2->setStatus(ArticleEntity::DRAFT);

        $this->article = $article;
        $this->article2 = $article2;

        $mappero = $this->container->getByType(\Ricaefeliz\Mappero\Mappero::class);
        $queryManager = $mappero->getQueryManager(ArticleEntity::class);
        $queryManager->save([$article, $article2]);

        Assert::notSame(NULL, $article->getId());
        Assert::notSame(NULL, $article2->getId());
    }



    public function testFind()
    {
        /** @var $mappero \Ricaefeliz\Mappero\Mappero */
        $mappero = $this->container->getByType(\Ricaefeliz\Mappero\Mappero::class);

        $queryManager = $mappero->getQueryManager(ArticleEntity::class);

        //Load data from db
        $article = $queryManager->findOneBy([
            "where" => [
                ["name", "=", "Článek pro test Mappero"]
            ]
        ]);
        $articles = $queryManager->findBy([
            "where" => [
                ["name", "LIKE", "%Mappero%"]
            ], "sort" => ["id", "DESC"]
        ]);

        //Check article from findOneBy..
        Assert::type(ArticleEntity::class, $article);
        Assert::same($this->article->getName(), $article->getName());
        Assert::same($this->article->getUrl(), $article->getUrl());
        Assert::same($this->article->getIntroduction(), $article->getIntroduction());
        Assert::same($this->article->getContent(), $article->getContent());
        Assert::same(ArticleEntity::DRAFT, $article->getStatus());

        //Check articles from findBy..
        Assert::same([$this->article2->getId(), $this->article->getId()], array_keys($articles));

        Assert::type(ArticleEntity::class, $articles[$this->article2->getId()]);
        Assert::same($this->article2->getName(), $articles[$this->article2->getId()]->getName());
        Assert::same($this->article2->getUrl(), $articles[$this->article2->getId()]->getUrl());
        Assert::same($this->article2->getIntroduction(), $articles[$this->article2->getId()]->getIntroduction());
        Assert::same($this->article2->getContent(), $articles[$this->article2->getId()]->getContent());
        Assert::same(ArticleEntity::DRAFT, $articles[$this->article2->getId()]->getStatus());

        Assert::type(ArticleEntity::class, $articles[$this->article->getId()]);
        Assert::same($this->article->getName(), $articles[$this->article->getId()]->getName());
        Assert::same($this->article->getUrl(), $articles[$this->article->getId()]->getUrl());
        Assert::same($this->article->getIntroduction(), $articles[$this->article->getId()]->getIntroduction());
        Assert::same($this->article->getContent(), $articles[$this->article->getId()]->getContent());
        Assert::same(ArticleEntity::DRAFT, $articles[$this->article->getId()]->getStatus());
    }



    public function testUpdate()
    {
        $mappero = $this->container->getByType(\Ricaefeliz\Mappero\Mappero::class);
        $queryManager = $mappero->getQueryManager(ArticleEntity::class);

        $article = clone $this->article;
        $article2 = clone $this->article2;

        $newNameArticle = "Nový změněný testovací článek pro Mappero";
        $newNameArticle2 = "Ještě jeden změněný testovací článek pro Mappero";

        //Change data
        $article->setName($newNameArticle);
        $article2->setName($newNameArticle2);

        //Save
        $queryManager->save([$article, $article2]);

        //Load changed articles from db
        $articleFromDb = $queryManager->findOneBy(["where" => [["id", "=", $article->getId()]]]);
        $article2FromDb = $queryManager->findOneBy(["where" => [["id", "=", $article2->getId()]]]);

        Assert::same($this->article->getId(), $article->getId());
        Assert::same($this->article2->getId(), $article2->getId());

        Assert::same($newNameArticle, $articleFromDb->getName());
        Assert::same($newNameArticle2, $article2FromDb->getName());
    }



    public function testCount()
    {
        $mappero = $this->container->getByType(\Ricaefeliz\Mappero\Mappero::class);
        $queryManager = $mappero->getQueryManager(ArticleEntity::class);

        $countDTO = $queryManager->count([
            "where" => [
                ["name", "LIKE", "%Mappero%"]
            ]
        ]);

        Assert::same(2, $countDTO->getCount());
    }



    public function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub

        $mappero = $this->container->getByType(\Ricaefeliz\Mappero\Mappero::class);
        $queryManager = $mappero->getQueryManager(ArticleEntity::class);

        $queryManager->remove([$this->article, $this->article2]);

        $articlesAfterRemove = $queryManager->findBy([
            "where" => [
                ["name", "LIKE", "%Mappero%"]
            ], "sort" => ["id", "DESC"]
        ]);

        Assert::null($articlesAfterRemove);
    }

}

(new Queries())->run();