<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Company\Preview;

use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Preview extends Control
{


    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}