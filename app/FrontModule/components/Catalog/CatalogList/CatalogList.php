<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Catalog\CatalogList;

use App\Catalog\Catalog;
use App\Catalog\CatalogRepository;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CatalogList extends Control
{


    /** @var CatalogRepository */
    private $catalogRepo;

    /** @var string|null */
    private $type;



    public function __construct(CatalogRepository $catalogRepo)
    {
        parent::__construct();
        $this->catalogRepo = $catalogRepo;
    }



    /**
     * @param $type string
     * @return self
    */
    public function setType(string $type) : self
    {
        $this->type = $type;
        return $this;
    }



    public function render()
    {
        $file = __DIR__ . '/templates/' . $this->type . '.latte';

        $this->template->items = $this->getItems();
        $this->template->setFile($file);
        $this->template->render();
    }



    /**
     * @return Catalog[]|array
     */
    private function getItems() : array
    {
        return $this->catalogRepo->findPublishedByType($this->type);
    }
}