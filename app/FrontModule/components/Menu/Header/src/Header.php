<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Menu\Header;

use App\Category\CategoryEntity;
use App\Category\CategoryRepository;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use App\FrontModule\Components\Menu\Header\Collection\Collection;
use App\FrontModule\Components\Menu\Header\Collection\Node;
use App\Helpers\Entities;
use App\Language\LanguageDTO;
use App\Page\PageEntity;
use App\Page\PageFacadeFactory;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Header extends Control
{


    /** @var CategoryRepository */
    private $categoryRepo;

    /** @var CategoryFiltrationGroupRepository */
    private $categoryFiltrationGroupRepo;

    /** @var LanguageDTO|null */
    private $language;

    /** @var PageFacadeFactory */
    private $pageFacadeFactory;



    public function __construct(CategoryFiltrationGroupRepository $categoryFiltrationGroupRepository,
                                CategoryRepository $categoryRepo,
                                PageFacadeFactory $pageFacadeFactory)
    {
        parent::__construct();
        $this->categoryFiltrationGroupRepo = $categoryFiltrationGroupRepository;
        $this->categoryRepo = $categoryRepo;
        $this->pageFacadeFactory = $pageFacadeFactory;
    }



    /**
     * @param $languageDTO LanguageDTO
     * @return self
     */
    public function setLanguage(LanguageDTO $languageDTO) : self
    {
        $this->language = $languageDTO;
        return $this;
    }



    public function render()
    {
        $langId = $this->language->getId();
        $categories = $this->categoryRepo->findPublishedByLanguageId($langId);

        $this->template->categories = $categories ? (new Collection(Node::create($categories)))->getItems() : [];
        $this->template->categoryParameterGroups = $categories ? $this->getCategoryParameterGroups($categories) : [];
        $this->template->pages = $this->pageFacadeFactory->create()->findPublishedParentsByLanguageIdAndMenuLocation($langId, PageEntity::MENU_LOCATION_HEADER);

        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @param $categories CategoryEntity[]
     * @return CategoryFiltrationGroupEntity[]|array
     */
    private function getCategoryParameterGroups(array $categories) : array
    {
        $categoryId = Entities::getProperty($categories, 'id');
        $groups = $this->categoryFiltrationGroupRepo->findByMoreCategoryIdForMenu($categoryId);
        return $groups ? Entities::toSegment($groups, 'categoryId') : [];
    }
}