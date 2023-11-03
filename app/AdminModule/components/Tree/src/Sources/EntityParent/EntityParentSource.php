<?php

declare(strict_types = 1);

namespace App\Components\Tree\Sources\EntityParent;

use App\Components\Tree\Sources\ISource;
use App\Components\Tree\Tree;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class EntityParentSource extends NObject implements ISource
{


    /** @var IEntityParent[]|null */
    protected $entities;

    /** @var Tree|null */
    protected $tree;



    public function __construct(array $entities = NULL)
    {
        $this->entities = $entities;
    }



    /**
     * @param Tree $tree
     * @return Tree
     */
    public function apply(Tree $tree) : Tree
    {
        if ($this->entities === NULL) {
            return $tree;
        }
        $this->tree = $tree;
        foreach ($this->entities as $entity) {
            $this->addItem($entity);
        }
        return $tree;
    }



    /**
     * @param IEntityParent $entity
     * @return IEntityParent
     */
    protected function addItem(IEntityParent $entity)
    {
        //top level
        if (!$entity->getParentId()) {
            //if not exists, create it..
            if (!$this->tree->getItem($entity->getId())) {
                $this->tree->addItem($entity->getId(), $entity->getTitle());
            }
        } //has parent
        else {
            //recursive
            $this->addItem($entity->getParentEntity());
            if (!$this->tree->getItem($entity->getId())) {
                $this->tree->addItem($entity->getId(), $entity->getTitle(), $entity->getParentId());
            }
        }
        return $entity;
    }

}