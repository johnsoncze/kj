<?php

declare(strict_types = 1);

namespace App\Components\PageBaseForm;

use App\Components\OgFormContainer\OgFormContainer;
use App\Components\OgFormContainer\OgFormContainerFactory;
use App\Components\SeoFormContainer\SeoFormContainer;
use App\Components\SeoFormContainer\SeoFormContainerFactory;
use App\Components\UrlFormContainer\UrlFormContainer;
use App\Components\UrlFormContainer\UrlFormContainerFactory;
use App\Helpers\Arrays;
use App\Helpers\Entities;
use App\Language\LanguageEntity;
use App\Page\PageAddFacadeException;
use App\Page\PageAddFacadeFactory;
use App\Page\PageEntity;
use App\Page\PageRepositoryFactory;
use App\Page\PageUpdateFacadeException;
use App\Page\PageUpdateFacadeFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class PageBaseForm extends Control
{


    /** @var PageEntity|null */
    protected $pageEntity;

    /** @var LanguageEntity|null */
    protected $languageEntity;

    /** @var PageAddFacadeFactory */
    protected $pageAddFacadeFactory;

    /** @var PageUpdateFacadeFactory */
    protected $pageUpdateFacadeFactory;

    /** @var SeoFormContainerFactory */
    protected $seoFormContainerFactory;

    /** @var OgFormContainerFactory */
    protected $ogFormContainerFactory;

    /** @var UrlFormContainerFactory */
    protected $urlFormContainerFactory;

    /** @var PageRepositoryFactory */
    protected $pageRepositoryFactory;

    /** @var Context */
    protected $database;

    /** @var string|null */
    protected $pageType = NULL;


    public function __construct(
        PageAddFacadeFactory $pageAddFacadeFactory,
        PageUpdateFacadeFactory $pageUpdateFacadeFactory,
        SeoFormContainerFactory $seoFormContainerFactory,
        UrlFormContainerFactory $urlFormContainerFactory,
        Context $context,
        PageRepositoryFactory $pageRepositoryFactory,
        OgFormContainerFactory $ogFormContainerFactory
    ) {
        $this->ogFormContainerFactory = $ogFormContainerFactory;
        parent::__construct();
        $this->pageAddFacadeFactory = $pageAddFacadeFactory;
        $this->pageUpdateFacadeFactory = $pageUpdateFacadeFactory;
        $this->seoFormContainerFactory = $seoFormContainerFactory;
        $this->urlFormContainerFactory = $urlFormContainerFactory;
        $this->database = $context;
        $this->pageRepositoryFactory = $pageRepositoryFactory;
    }



    /**
     * @param LanguageEntity $languageEntity
     * @return $this
     */
    public function setLanguageEntity(LanguageEntity $languageEntity)
    {
        $this->languageEntity = $languageEntity;
        return $this;
    }



    /**
     * @return LanguageEntity
     * @throws PageBaseFormException
     */
    public function getLanguageEntity() : LanguageEntity
    {
        if (!$entity = $this->languageEntity) {
            throw new PageBaseFormException("You must set language entity.");
        }
        return $entity;
    }



    /**
     * @param PageEntity $pageEntity
     * @return $this
     */
    public function setPageEntity(PageEntity $pageEntity)
    {
        $this->pageEntity = $pageEntity;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $menuList = PageEntity::getMenuLocationList();
        $templateList = PageEntity::getTemplateList();

        //Create form
        $form = new Form();
        $form->addText("language", "Jazyk")
            ->setAttribute("class", "form-control")
            ->setDisabled(TRUE);
        $form->addText("type", "Typ stránky")
            ->setAttribute("class", "form-control")
            ->setDisabled(TRUE);
        $form->addSelect('menu', 'Menu*', $menuList)
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyberte menu')
            ->setPrompt('- Vyberte -');
        $form->addText("name", "Název")
            ->setRequired("Vyplňte název stránky")
            ->setAttribute("class", "form-control")
            ->setAttribute('autofocus');
        $form->addSelect("status", "Stav", Arrays::toPair(PageEntity::getStatuses(), "key", "translate"))
            ->setAttribute("class", "form-control");
        $form->addSelect("parentPageId", "Nadřazená stránka", $this->getPageList())
            ->setAttribute("class", "form-control")
            ->setPrompt("- Vyberte -");
        $form->addSelect('template', 'Grafická šablona')
            ->setPrompt('- vyberte -')
            ->setAttribute('class', 'form-control')
            ->setItems($templateList);

        //Add components
        $form->addComponent($this->urlFormContainerFactory->create(), UrlFormContainer::NAME);
        $form->addComponent($this->seoFormContainerFactory->create(), SeoFormContainer::NAME);
        $form->addComponent($this->ogFormContainerFactory->create(), OgFormContainer::NAME);

        //Add configuration
        $this->formConfiguration($form);

        //Set default values
        $this->setFormDefaultValues($form, $templateList);

        $form->addSubmit("submit", "Uložit")
            ->setAttribute("class", "btn btn-success");
        $form->onSuccess[] = [$this, "formSuccess"];
        return $form;
    }



    /**
     * @param Form $form
     * @param $templateList array
     * @return Form
     */
    protected function setFormDefaultValues(Form $form, array $templateList)
    {
        //Default for disabled inputs
        $form->setDefaults([
            "language" => $this->getLanguageEntity()->getName(),
            "type" => PageEntity::getTypes()[$this->getPageType()]["translate"]
        ]);

        //Default values
        if ($this->pageEntity) {
            $form->setDefaults([
                'menu' => $this->pageEntity->getMenuLocation(),
                "name" => $this->pageEntity->getName(),
                "parentPageId" => $this->pageEntity->getParentPageId(),
                "status" => $this->pageEntity->getStatus(),
                'template' => $this->pageEntity instanceof PageEntity && $this->pageEntity->getTemplate() && array_key_exists($this->pageEntity->getTemplate(), $templateList) ? $this->pageEntity->getTemplate() : NULL,
                UrlFormContainer::NAME => [
                    "url" => $this->pageEntity->getUrl()
                ], SeoFormContainer::NAME => [
                    "titleSeo" => $this->pageEntity->getTitleSeo(),
                    "descriptionSeo" => $this->pageEntity->getDescriptionSeo()
                ]
            ]);
        }

        return $form;
    }



    /**
     * @param Form $form
     */
    public function formSuccess(Form $form)
    {
        $pageValues = $this->getPageValues($form);
        if (!$this->pageEntity instanceof PageEntity) {
            $this->saveNewPage($form, $pageValues);
        } else {
            $this->saveExistsPage($form, $pageValues);
        }
    }



    /**
     * @param Form $form
     * @param PageValues $values
     */
    public function saveNewPage(Form $form, PageValues $values)
    {
        try {
            $this->database->beginTransaction();
            $addFacade = $this->pageAddFacadeFactory->create();
            $pageEntity = $addFacade->add($values->languageId, $values->parentPageId, $values->type,
                $values->name, $values->content, $values->url, $values->titleSeo,
                $values->descriptionSeo, $values->setting, $values->status, $values->template, $values->menuLocation);
            $this->onSave($form, $pageEntity, TRUE);
            $this->database->commit();

            //Redirect
            $this->presenter->flashMessage("Stránka byla uložena.", "success");
            $this->presenter->redirect("Page:edit", ["id" => $pageEntity->getId()]);
        } catch (PageAddFacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        } catch (PageUpdateFacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        }
    }



    /**
     * @param Form $form
     * @param PageValues $pageValues
     */
    public function saveExistsPage(Form $form, PageValues $pageValues)
    {
        try {
            //Set values
            $pageEntity = $this->pageEntity;
            $pageEntity->setName($pageValues->name);
            $pageEntity->setContent($pageValues->content);
            $pageEntity->setUrl($pageValues->url);
            $pageEntity->setTitleSeo($pageValues->titleSeo);
            $pageEntity->setDescriptionSeo($pageValues->descriptionSeo);
            $pageEntity->setSetting($pageValues->setting);
            $pageEntity->setStatus($pageValues->status);
            $pageEntity->setTemplate($pageValues->template);
            $pageEntity->setMenuLocation($pageValues->menuLocation);
            $pageEntity->setTitleOg($pageValues->titleOg);
            $pageEntity->setDescriptionOg($pageValues->descriptionOg);

            //Save
            $this->database->beginTransaction();
            $updateFacade = $this->pageUpdateFacadeFactory->create();
            $updateFacade->update($pageEntity);
            $this->onSave($form, $pageEntity, FALSE);
            $this->database->commit();

            //Redirect
            $this->presenter->flashMessage("Stránka byla uložena.", "success");
            $this->presenter->redirect("this");
        } catch (PageUpdateFacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        }
    }



    /**
     * @param Form $form
     * @return PageValues
     */
    public function getPageValues(Form $form) : PageValues
    {
        //Values
        $values = $form->getValues();
        $urlValues = $values->{UrlFormContainer::NAME};
        $seoValues = $values->{SeoFormContainer::NAME};
        $ogValues = $values->{OgFormContainer::NAME};

        //Create object
        $pageValues = new PageValues();
        $pageValues->languageId = $this->getLanguageEntity()->getId();
        $pageValues->type = $this->getPageType();
        $pageValues->parentPageId = $values->parentPageId;
        $pageValues->name = $values->name;
        $pageValues->url = $urlValues->url;
        $pageValues->titleSeo = $seoValues->titleSeo;
        $pageValues->descriptionSeo = $seoValues->descriptionSeo;
        $pageValues->titleOg = $ogValues->titleOg;
        $pageValues->descriptionOg = $ogValues->descriptionOg;
        $pageValues->status = $values->status;
        $pageValues->menuLocation = $values->menu;
        $pageValues->template = $values->template;

        return $pageValues;
    }



    /**
     * Get list of pages for choose a parent page
     * @return array
     */
    protected function getPageList() : array
    {
        $repo = $this->pageRepositoryFactory->create();
        $pages = $repo->findByLangIdWithoutPageId($this->getLanguageEntity()->getId(), ($this->pageEntity ? $this->pageEntity->getId() : NULL), FALSE);

        return Entities::toPair(($pages ? $pages : []), "id", "name");
    }



    /**
     * @return string
     * @throws PageBaseFormException
     */
    protected function getPageType() : string
    {
        if (!$type = $this->pageType) {
            throw new PageBaseFormException('You must set type of page into property "$pageType".');
        }
        return $type;
    }



    /**
     * @param Form $form
     * @return void
     */
    protected abstract function formConfiguration(Form $form);



    /**
     * @param Form $form
     * @param PageEntity $entity
     * @param $newPage bool
     */
    protected function onSave(Form $form, PageEntity $entity, bool $newPage)
    {
    }



    /**
     * Render form
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->pageEntity = $this->pageEntity;
        $this->template->render();
    }
}