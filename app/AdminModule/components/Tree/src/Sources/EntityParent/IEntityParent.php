<?php

namespace App\Components\Tree\Sources\EntityParent;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IEntityParent
{


    /**
     * @return int|string
     */
    public function getId();



    /**
     * Get id of parent
     * @return int|string|null
     */
    public function getParentId();



    /**
     * Get parent entity
     * @return IEntityParent|null
     */
    public function getParentEntity();



    /**
     * @return int|string
     */
    public function getTitle();
}