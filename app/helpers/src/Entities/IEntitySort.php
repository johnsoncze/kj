<?php

namespace App\Helpers;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IEntitySort
{


    /**
     * @param $sort int
     * @return self
     */
    public function setSort($sort);



    /**
     * @return int
     */
    public function getSort();
}