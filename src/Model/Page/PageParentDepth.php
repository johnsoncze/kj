<?php

namespace App\Page;


use App\Components\Tree\Sources\EntityParent\EntityParentDepth;
use App\Components\Tree\Sources\EntityParent\EntityParentDepthException;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageParentDepth extends NObject
{


    /**
     * @param PageEntity $pageEntity
     * @return PageEntity
     * @throws PageParentDepthException
     */
    public function checkDepth(PageEntity $pageEntity) : PageEntity
    {
        try {
            $entityParentDepth = new EntityParentDepth();
            $entityParentDepth->checkDepth($pageEntity, PageEntity::MAX_PARENT_DEPTH);
            return $pageEntity;
        } catch (EntityParentDepthException $exception) {
            throw new PageParentDepthException("Maximální počet zanoření stránek jsou " . PageEntity::MAX_PARENT_DEPTH . " úrovně.");
        }
    }
}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageParentDepthException extends \Exception
{


}