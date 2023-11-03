<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Pagination;

use Nette\Application\UI\Control;
use Nette\Utils\Paginator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Pagination extends Control
{


    /** @var Paginator */
    private $paginator;



    public function __construct(Paginator $paginator)
    {
        parent::__construct();
        $this->paginator = $paginator;
    }



    public function render()
    {
        $this->template->paginator = $this->paginator;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}