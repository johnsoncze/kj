<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltrationGroup;

use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntityFactory;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupSetSiteMap;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SetSiteMapTest extends BaseTestCase
{


    public function testEnableSiteMap()
    {
        $categoryId = 55;
        $description = 'This is a description.';
        $titleSeo = 'Title for SEO';
        $descriptionSeo = 'Description for SEO';
        $index = TRUE;
        $follow = FALSE;

        $entityFactory = new CategoryFiltrationGroupEntityFactory();
        $group = $entityFactory->create($categoryId, $description, $titleSeo, $descriptionSeo,
            $index, NULL, $follow, CategoryFiltrationGroupEntity::PUBLISH);

        $setter = new CategoryFiltrationGroupSetSiteMap();
        $setter->set($group);

        Assert::true($group->getSiteMap());
    }



    public function testDisableSiteMap()
    {
        $categoryId = 55;
        $description = 'This is a description.';
        $titleSeo = 'Title for SEO';
        $descriptionSeo = 'Description for SEO';
        $index = FALSE;
        $follow = FALSE;

        $entityFactory = new CategoryFiltrationGroupEntityFactory();
        $group = $entityFactory->create($categoryId, $description, $titleSeo, $descriptionSeo,
            $index, NULL, $follow, CategoryFiltrationGroupEntity::PUBLISH);

        $setter = new CategoryFiltrationGroupSetSiteMap();
        $setter->set($group);

        Assert::false($group->getSiteMap());
    }
}

(new SetSiteMapTest())->run();