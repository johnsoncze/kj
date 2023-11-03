<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltrationGroup;

use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntityFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class EntityFactoryTest extends BaseTestCase
{


    public function testCreate()
    {
        $categoryId = 55;
        $description = 'This is a description.';
        $titleSeo = 'Title for SEO';
        $descriptionSeo = 'Description for SEO';
        $index = FALSE;
        $siteMap = TRUE;
        $follow = FALSE;

        $entityFactory = new CategoryFiltrationGroupEntityFactory();
        $group = $entityFactory->create($categoryId, $description, $titleSeo, $descriptionSeo,
            $index, $siteMap, $follow, CategoryFiltrationGroupEntity::PUBLISH);

        Assert::type(CategoryFiltrationGroupEntity::class, $group);
        Assert::same($categoryId, $group->getCategoryId());
        Assert::same($description, $group->getDescription());
        Assert::same($titleSeo, $group->getTitleSeo());
        Assert::same($descriptionSeo, $group->getDescriptionSeo());
        Assert::same($index, $group->getIndexSeo());
        Assert::same($siteMap, $group->getSiteMap());
        Assert::same($follow, $group->getFollowSeo());
    }
}

(new EntityFactoryTest())->run();