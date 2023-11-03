<?php

declare(strict_types = 1);

namespace App\Article\Module;

use App\BaseEntity;
use App\Page\PageEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="article_module")
 *
 * @method setName($name)
 * @method getName()
 * @method getPage()
 */
class Module extends BaseEntity implements IEntity
{


    /** @var int */
    const NEWS = 1;
    const BLOG = 2;

    /**
     * @Column(name="am_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="am_name")
     */
    protected $name;

    /**
     * @var PageEntity|null
     */
    protected $page;



    /**
     * @param $page PageEntity
     * @return self
     */
    public function setPage(PageEntity $page): self
    {
        $this->page = $page;
        return $this;
    }
}