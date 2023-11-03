<?php

namespace App\FrontModule\Components\Feed\AbstractFeed;

use App\Product\Product;
use App\Product\ProductDTO;
use App\Product\ProductDTOFactory;
use App\Product\ProductPublishedRepositoryFactory;
use Nette\Application\UI\Control;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;

abstract class AbstractFeed extends Control
{

    /** @var string */
    protected $templatePath;

    public function render()
    {
        $this->template->setFile($this->templatePath);
        $this->template->render();
    }


    /**
     * @return string
     */
    public function renderToString(): string
    {
        $this->template->setFile($this->templatePath);
        return (string)$this->template;
    }

}