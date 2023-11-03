<?php

declare(strict_types = 1);

namespace App\Product\Variant\Tree;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IGroupCollection
{


    /**
     * @param $group Group
     * @return Group
     */
    public function addGroup(Group $group);



    /**
     * @param $id int
     * @return Group|null
     */
    public function getGroupById(int $id);
}