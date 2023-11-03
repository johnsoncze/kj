<?php

declare(strict_types = 1);

namespace App\Tests\Page;

require_once __DIR__ . "/../bootstrap.php";

\Tester\Environment::lock("database", TEMP_TEST);

use App\NotFoundException;
use App\Page\PageAddFacade;
use App\Page\PageAddFacadeException;
use App\Page\PageAddFacadeFactory;
use App\Page\PageEntity;
use App\Page\PageRemoveFacadeFactory;
use App\Page\PageRepository;
use App\Page\PageRepositoryFactory;
use App\Page\PageUpdateFacade;
use App\Page\PageUpdateFacadeException;
use App\Page\PageUpdateFacadeFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageAddUpdateRemoveFacadeTest extends BaseTestCase
{


    use PageTestTrait;

    /** @var PageEntity|null */
    protected $page;

    /** @var PageEntity|null */
    protected $page2;

    /** @var PageRepository|null */
    protected $pageRepository;



    public function setUp()
    {
        parent::setUp();

        //Parametes
        $type = PageEntity::ARTICLES_TYPE;
        $name = "Stránka 123456789";
        $content = "Obsah stránky!";
        $url = "url Adřesa !! 7878979879";
        $titleSeo = "Seo titulek";
        $descriptionSeo = "Popis SEO";
        $setting = ["alkohol" => FALSE, "počet jednotek" => 778, "max_price" => 457.78, "jméno" => "Libuše"];
        $status = PageEntity::DRAFT;

        //Add data
        /** @var $addFacade PageAddFacade */
        $addFacade = $this->container->getByType(PageAddFacadeFactory::class)->create();
        $page = $addFacade->add(1, NULL, $type, $name, $content, $url, $titleSeo, $descriptionSeo, $setting, $status, NULL, PageEntity::MENU_LOCATION_FOOTER_PURCHASE);

        //Load from db
        $pageRepository = $this->container->getByType(PageRepositoryFactory::class)->create();
        /** @var $pageFromDb PageEntity */
        $pageFromDb = $pageRepository->getOneById($page->getId(), FALSE);

        //Set to class
        $this->page = $pageFromDb;
        $this->pageRepository = $pageRepository;

        //Tests
        Assert::type("int", $page->getId());
        Assert::type(PageEntity::class, $page);
        Assert::type(PageEntity::class, $pageFromDb);
        Assert::same($type, $pageFromDb->getType());
        Assert::same($name, $pageFromDb->getName());
        Assert::same($content, $pageFromDb->getContent());
        Assert::same($page->getUrl(), $pageFromDb->getUrl());
        Assert::same($titleSeo, $pageFromDb->getTitleSeo());
        Assert::same($descriptionSeo, $pageFromDb->getDescriptionSeo());
        Assert::same($setting, $pageFromDb->getSetting(TRUE));
        Assert::same($status, $pageFromDb->getStatus());
        Assert::same(PageEntity::MENU_LOCATION_FOOTER_PURCHASE, $pageFromDb->getMenuLocation());
    }



    public function testUpdate()
    {
        $name = "Nový název stránky 55";
        $content = "Nový obsah stránky 55";
        $url = "nová URL";
        $titleSeo = "Nový titulek pro SEO";
        $descriptionSeo = "Nový popis pro SEO";
        $setting = ["čepice" => "ano", "bool" => FALSE, "další parametr" => 4455.45];
        $status = PageEntity::PUBLISH;

        $page = clone $this->page;
        $page->setName($name);
        $page->setContent($content);
        $page->setUrl($url);
        $page->setTitleSeo($titleSeo);
        $page->setDescriptionSeo($descriptionSeo);
        $page->setSetting($setting);
        $page->setStatus($status);
        $page->setMenuLocation(PageEntity::MENU_LOCATION_HEADER);

        //Update
        /** @var $updateFacade PageUpdateFacade */
        $updateFacade = $this->container->getByType(PageUpdateFacadeFactory::class)->create();
        $pageAfterUpdate = $updateFacade->update($page);

        //Load from db
        $pageRepository = $this->container->getByType(PageRepositoryFactory::class)->create();
        /** @var $pageFromDb PageEntity */
        $pageFromDb = $pageRepository->getOneById($this->page->getId());

        //Tests
        Assert::same(PageEntity::class, $pageAfterUpdate->getClassName());
        Assert::same($pageAfterUpdate->getId(), $pageFromDb->getId());
        Assert::same($name, $pageFromDb->getName());
        Assert::same($content, $pageFromDb->getContent());
        Assert::same($pageAfterUpdate->getUrl(), $pageFromDb->getUrl());
        Assert::same($titleSeo, $pageFromDb->getTitleSeo());
        Assert::same($descriptionSeo, $pageFromDb->getDescriptionSeo());
        Assert::same($setting, $pageFromDb->getSetting(TRUE));
        Assert::same($status, $pageFromDb->getStatus());
        Assert::same(PageEntity::MENU_LOCATION_HEADER, $pageFromDb->getMenuLocation());
    }



    public function testAddPageWithSameName()
    {
        /** @var $addFacade PageAddFacade */
        $addFacade = $this->container->getByType(PageAddFacadeFactory::class)->create();

        Assert::exception(function () use ($addFacade) {
            $addFacade->add($this->page->getLanguageId(),
                NULL, $this->page->getType(), $this->page->getName(),
                $this->page->getContent(), "123456789", NULL, NULL, NULL, $this->page->getStatus(), NULL, PageEntity::MENU_LOCATION_HEADER);
        }, PageAddFacadeException::class);
    }



    public function testUpdatePageWithSameName()
    {
        //Prepare data
        /** @var $addFacade PageAddFacade */
        $addFacade = $this->container->getByType(PageAddFacadeFactory::class)->create();
        $page = $addFacade->add(1, NULL, PageEntity::ARTICLES_TYPE, "Název stránky, který bude později změněn",
            NULL, NULL, NULL, NULL, NULL, PageEntity::DRAFT, NULL, PageEntity::MENU_LOCATION_HEADER);
        $this->addEntityForRemove($page, $this->pageRepository);

        //Set into class for remove
        $this->page2 = $page;

        $pageWithSameName = clone $page;
        $pageWithSameName->setName($this->page->getName());

        $updateFacade = $this->container->getByType(PageUpdateFacadeFactory::class)->create();

        Assert::exception(function () use ($pageWithSameName, $updateFacade) {
            $updateFacade->update($pageWithSameName);
        }, PageUpdateFacadeException::class);
    }



    public function tearDown()
    {
        parent::tearDown();

        $page = $this->page;
        $pageRepository = $this->container->getByType(PageRepositoryFactory::class)->create();

        //Remove
        $removeFacade = $this->container->getByType(PageRemoveFacadeFactory::class)->create();
        $removeFacade->remove($page->getId());

        //Test
        Assert::exception(function () use ($pageRepository, $page) {
            $pageRepository->getOneById($page->getId());
        }, NotFoundException::class);
    }
}

(new PageAddUpdateRemoveFacadeTest())->run();