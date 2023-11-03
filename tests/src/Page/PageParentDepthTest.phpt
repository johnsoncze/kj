<?php

namespace App\Tests\Page;

use App\Page\PageEntity;
use App\Page\PageParentDepth;
use App\Page\PageParentDepthException;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../bootstrap.php";

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageParentDepthTest extends BaseTestCase
{


    public function testCheckDepthSuccess()
    {
        $page1 = new PageEntity();
        $page2 = new PageEntity();
        $page2->setParentPage($page1);

        $page = new PageEntity();
        $page->setParentPage($page1);

        $pageDepth = new PageParentDepth();

        Assert::type(PageEntity::class, $pageDepth->checkDepth($page));
    }



    public function testCheckDepthFail()
    {
        $page1 = new PageEntity();
        $page2 = new PageEntity();
        $page3 = new PageEntity();
        $page = new PageEntity();

        $page2->setParentPage($page1);
        $page3->setParentPage($page2);
        $page->setParentPage($page3);

        $pageDepth = new PageParentDepth();

        Assert::exception(function () use ($page, $pageDepth) {
            $pageDepth->checkDepth($page);
        }, PageParentDepthException::class);
    }
}

(new PageParentDepthTest())->run();