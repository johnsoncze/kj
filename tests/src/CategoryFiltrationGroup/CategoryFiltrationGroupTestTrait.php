<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltrationGroup;

use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait CategoryFiltrationGroupTestTrait
{


    /**
     * @return CategoryFiltrationGroupEntity
     */
    private function createTestGroup() : CategoryFiltrationGroupEntity
    {
        $group = new CategoryFiltrationGroupEntity();
        $group->setCategoryId(1);
        $group->setDescription('Group description.');
        $group->setShowInMenu(TRUE);
        $group->setIndexSeo(TRUE);
        $group->setFollowSeo(TRUE);
        $group->setSiteMap(TRUE);
        $group->setStatus(CategoryFiltrationGroupEntity::PUBLISH);

        return $group;
    }
}