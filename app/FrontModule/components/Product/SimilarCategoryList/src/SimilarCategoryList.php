<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\SimilarCategoryList;

use App\CategoryFiltrationGroup\Similar\SimilarFacadeFactory;
use App\Language\LanguageEntity;
use App\Product\Product;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SimilarCategoryList extends Control
{


    /** @var SimilarFacadeFactory */
    private $groupSimilarFacadeFactory;

    /** @var LanguageEntity|null */
    private $language;

    /** @var Product|null */
    private $product;



    public function __construct(SimilarFacadeFactory $similarFacadeFactory)
    {
        parent::__construct();
        $this->groupSimilarFacadeFactory = $similarFacadeFactory;
    }



    /**
     * @param LanguageEntity $language
     * @return self
     */
    public function setLanguage(LanguageEntity $language) : self
    {
        $this->language = $language;
        return $this;
    }



    /**
     * @param $product Product
     * @return self
     */
    public function setProduct(Product $product) : self
    {
        $this->product = $product;
        return $this;
    }



    public function render()
    {
        $groupSimilarFacade = $this->groupSimilarFacadeFactory->create();

        $this->template->groups = $groupSimilarFacade->findByProductIdAndLanguageId($this->product->getId(), $this->language->getId());
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}