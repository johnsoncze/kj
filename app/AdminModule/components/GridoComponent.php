<?php

namespace App\Components;

use App\Extensions\Grido\GridoFactory;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class GridoComponent extends Control
{


    const HIGHLIGHT_ROW_BACKGROUND_COLOR = '#d7d594';

    /** @var GridoFactory */
    protected $gridoFactory;

    /** @var object repository factory */
    protected $repositoryFactory;



    public function __construct(GridoFactory $gridoFactory)
    {
        parent::__construct();
        $this->gridoFactory = $gridoFactory;
    }



    /**
     * @param $factory object
     * @return self
     */
    public function setRepositoryFactory($factory)
    {
        $this->repositoryFactory = $factory;
        return $this;
    }



    /**
     * @param $gridoFactory GridoFactory
     * @return self
     */
    public function setGridoFactory(GridoFactory $gridoFactory)
    {
        $this->gridoFactory = $gridoFactory;
        return $this;
    }
}