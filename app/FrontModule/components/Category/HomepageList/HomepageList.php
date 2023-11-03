<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\HomepageList;

use App\Category\CategoryRepository;
use App\Language\LanguageEntity;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class HomepageList extends Control
{


    /** @var CategoryRepository */
    private $categoryRepo;

    /** @var LanguageEntity|null */
    private $language;



    public function __construct(CategoryRepository $categoryRepo)
    {
        parent::__construct();
        $this->categoryRepo = $categoryRepo;
    }



    /**
     * @param $language LanguageEntity
     * @return self
     */
    public function setLanguage(LanguageEntity $language) : self
    {
        $this->language = $language;
        return $this;
    }



    public function render()
    {
        $this->template->categories = $this->categoryRepo->findPublishedForHomepageByLanguageId($this->language->getId());
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}