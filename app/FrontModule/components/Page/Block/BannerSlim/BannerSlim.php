<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\Block\BannerSlim;

use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class BannerSlim extends Control
{


    /** @var Item */
    private $item;



    public function __construct(Item $item)
    {
        parent::__construct();
        $this->item = $item;
    }



    public function render()
    {
        $this->template->item = $this->item;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}