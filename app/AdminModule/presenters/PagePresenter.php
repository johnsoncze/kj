<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Page\Form\Sort\SortForm;
use App\AdminModule\Components\Page\Form\Sort\SortFormFactory;
use App\Components\AdminPageNavigationTree\AdminPageNavigationTree;
use App\Components\AdminPageNavigationTree\AdminPageNavigationTreeFactory;
use App\Components\ChooseLanguageForm\ChooseLanguageForm;
use App\Components\ChooseLanguageForm\ChooseLanguageFormFactory;
use App\Components\PageArticlesForm\PageArticlesForm;
use App\Components\PageArticlesForm\PageArticlesFormFactory;
use App\Components\PageList\PageListFactory;
use App\Components\PageTextForm\PageTextFormFactory;
use App\Helpers\Arrays;
use App\Language\LanguageEntity;
use App\Language\LanguageRepositoryFactory;
use App\NotFoundException;
use App\Page\PageEntity;
use App\Page\PageRepositoryFactory;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PagePresenter extends AdminModulePresenter
{


    /** @var LanguageEntity|null */
    protected $languageEntity;

    /** @var PageEntity|null */
    protected $pageEntity;

    /** @var ChooseLanguageFormFactory @inject */
    public $languageFormFactory;

    /** @var PageTextFormFactory @inject */
    public $pageTextFormFactory;

    /** @var PageArticlesFormFactory @inject */
    public $pageArticlesFormFactory;

    /**  @var PageRepositoryFactory @inject */
    public $pageRepositoryFactory;

    /** @var PageListFactory @inject */
    public $pageListFactory;

    /** @var SortFormFactory @inject */
    public $pageSortFormFactory;

    /** @var AdminPageNavigationTreeFactory @inject */
    public $adminPageNavigationTreeFactory;



    /**
     * @param int $langId
     * @param string $type
     * @throws BadRequestException
     */
    public function actionAdd(int $langId = NULL, string $type = NULL)
    {
        if (!$langId || !$type) {
            $this->template->setFile(__DIR__ . "/templates/Page/templates/preAdd.latte");
        } else {
            //Check language
            $this->languageEntity = $this->checkRequest((int)$langId, LanguageRepositoryFactory::class);

            //Check type of page
            if (!in_array($type, array_keys(PageEntity::getTypes()))) {
                throw new BadRequestException("Neznámý typ stránky '{$type}'.", 404);
            }

            //Variables for template
            $this->template->type = ucfirst($type);
        }
    }



    /**
     * @param int $id
     * @throws BadRequestException
     */
    public function actionEdit(int $id)
    {
        try {
            //Check page
            $pageRepository = $this->pageRepositoryFactory->create();
            $pageEntity = $pageRepository->getOneById($id, FALSE);
            $this->pageEntity = $pageEntity;

            //Check language
            $this->languageEntity = $this->checkRequest((int)$this->pageEntity->getLanguageId(), LanguageRepositoryFactory::class);
        } catch (NotFoundException $exception) {
            throw new BadRequestException(null, 404);
        }
        $this->template->setFile(__DIR__ . "/templates/Page/add.latte");
        $this->template->type = ucfirst($this->pageEntity->getType());
    }



    /**
     * @param $languageId int|null
     * @param $source string
     * @return void
     */
    public function actionSort(int $languageId = NULL, string $source = NULL)
    {
        if ($languageId === NULL) {
            $this->template->setFile(__DIR__ . '/templates/Page/templates/preSort.latte');
        } else {
            $this->languageEntity = $this->checkRequest((int)$languageId, LanguageRepositoryFactory::class);
        }
    }



    /**
     * @return \App\Components\ChooseLanguageForm\ChooseLanguageForm
     */
    public function createComponentLanguageForm()
    {
        $form = $this->languageFormFactory->create();

        //Create select box for choose type of page
        $form->createFormCallback(function (Form $form) {
            $items = PageEntity::getTypes();
            $items = Arrays::toPair($items, "key", "translate");
            $form->addSelect("pageType", "Typ stránky", $items)
                ->setRequired("Vyberte typ stránky")
                ->setAttribute("class", "form-control")
                ->setPrompt("- Vyberte -");
        });

        //On success event
        $form->addOnSuccess(function (Form $form) {
            $values = $form->getValues();
            $this->redirect("Page:add", [
                "langId" => $values->language,
                "type" => $values->pageType
            ]);
        });

        return $form;
    }



    /**
     * @return \App\Components\PageTextForm\PageTextForm
     */
    public function createComponentPageTextForm()
    {
        $form = $this->pageTextFormFactory->create();
        $form->setLanguageEntity($this->languageEntity);
        if ($this->pageEntity instanceof PageEntity) {
            $form->setPageEntity($this->pageEntity);
        }
        return $form;
    }



    /**
     * @return PageArticlesForm
     */
    public function createComponentPageArticlesForm()
    {
        $form = $this->pageArticlesFormFactory->create();
        $form->setLanguageEntity($this->languageEntity);
        if ($this->pageEntity instanceof PageEntity) {
            $form->setPageEntity($this->pageEntity);
        }
        return $form;
    }



    /**
     * @return \App\Components\PageList\PageList
     */
    public function createComponentPageList()
    {
        return $this->pageListFactory->create();
    }



    /**
     * @return SortForm
     */
    public function createComponentPageSortForm() : SortForm
    {
        $form = $this->pageSortFormFactory->create();
        $form->setLanguage($this->languageEntity);
        return $form;
    }



    /**
     * @return AdminPageNavigationTree
     */
    public function createComponentAdminPageNavigationTree() : AdminPageNavigationTree
    {
        return $this->adminPageNavigationTreeFactory->create();
    }



    /**
     * @return ChooseLanguageForm
     * @throws AbortException
     */
    public function createComponentSortLanguageForm() : ChooseLanguageForm
    {
        $form = $this->languageFormFactory->create();
        $form->addOnSuccess(function (Form $form) {
            $values = $form->getValues();
            $parameters['languageId'] = $values->language;
            $parameters['source'] = PageEntity::MENU_LOCATION_HEADER;
            $this->redirect('Page:sort', $parameters);
        });
        return $form;
    }
}