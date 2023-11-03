<?php

namespace App\Components\ArticleCategoryForm;

use App\Article\Module\Module;
use App\Article\Module\ModuleRepository;
use App\ArticleCategory\ArticleCategoryEntity;
use App\ArticleCategory\ArticleCategoryFacadeFactory;
use App\Components\SeoFormContainer\SeoFormContainer;
use App\Components\SeoFormContainer\SeoFormContainerFactory;
use App\Components\UrlFormContainer\UrlFormContainer;
use App\Components\UrlFormContainer\UrlFormContainerFactory;
use App\FacadeException;
use App\Helpers\Entities;
use App\Language\LanguageEntity;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCategoryForm extends Control
{


    /** @var Context */
    protected $database;

    /** @var ArticleCategoryFacadeFactory */
    protected $articleCategoryFacadeFactory;

    /** @var ModuleRepository */
    protected $moduleRepo;

    /** @var SeoFormContainerFactory */
    protected $seoFormContainerFactory;

    /** @var UrlFormContainerFactory */
    protected $urlFormContainerFactory;

    /** @var LanguageEntity */
    protected $languageEntity;

    /** @var ArticleCategoryEntity */
    protected $articleCategoryEntity;



    public function __construct(ArticleCategoryFacadeFactory $articleCategoryFacadeFactory,
                                Context $context,
                                ModuleRepository $moduleRepository,
                                SeoFormContainerFactory $seoFormContainerFactory,
                                UrlFormContainerFactory $urlFormContainerFactory)
    {
        parent::__construct();
        $this->articleCategoryFacadeFactory = $articleCategoryFacadeFactory;
        $this->database = $context;
        $this->moduleRepo = $moduleRepository;
        $this->seoFormContainerFactory = $seoFormContainerFactory;
        $this->urlFormContainerFactory = $urlFormContainerFactory;
    }



    /**
     * @param $languageEntity LanguageEntity
     * @return self
     */
    public function setLanguageEntity(LanguageEntity $languageEntity)
    {
        $this->languageEntity = $languageEntity;
        return $this;
    }



    /**
     * @param $articleCategoryEntity ArticleCategoryEntity
     * @return self
     */
    public function setArticleCategoryEntity(ArticleCategoryEntity $articleCategoryEntity)
    {
        $this->articleCategoryEntity = $articleCategoryEntity;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();
        $form->addText("language", "Jazyk")
            ->setAttribute("class", "form-control")
            ->setDisabled(true)
            ->setDefaultValue($this->languageEntity->getName());
        $form->addSelect('module', 'Modul*')
            ->setPrompt('- vyberte -')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyberte modul.')
            ->setItems($this->getModuleList());
        $form->addText("name", "Název*")
            ->setRequired("Vložte název.")
            ->setAttribute("class", "form-control")
            ->setAttribute('autofocus');
        $form->addComponent($this->urlFormContainerFactory->create(), UrlFormContainer::NAME);
        $form->addComponent($this->seoFormContainerFactory->create(), SeoFormContainer::NAME);
        $form->addSubmit("submit", "Uložit")
            ->setAttribute("class", "btn btn-success");
        $form->onSuccess[] = [$this, "formSuccess"];
        if ($this->articleCategoryEntity) {
            $this->setDefault($form);
        }
        return $form;
    }



    /**
     * @param $form Form
     * @return Form
     */
    protected function setDefault(Form $form)
    {
        $form->setDefaults([
            "language" => $this->languageEntity->getName(),
            "name" => $this->articleCategoryEntity->getName(),
            'module' => $this->articleCategoryEntity->getModuleId(),
            UrlFormContainer::NAME => [
                "url" => $this->articleCategoryEntity->getUrl()
            ],
            SeoFormContainer::NAME => [
                "titleSeo" => $this->articleCategoryEntity->getTitleSeo(),
                "descriptionSeo" => $this->articleCategoryEntity->getDescriptionSeo()
            ]
        ]);
        return $form;
    }



    /**
     * @param $form Form
     * @return void
     */
    public function formSuccess(Form $form)
    {
        try {
            $values = $form->getValues();
            $valuesUrl = $values->{UrlFormContainer::NAME};
            $valuesSeo = $values->{SeoFormContainer::NAME};
            $this->database->beginTransaction();
            if (!$this->articleCategoryEntity) {
                $entity = $this->articleCategoryFacadeFactory->create()->add($this->languageEntity, $values->module, $values->name, $valuesUrl->url, $valuesSeo->titleSeo, $valuesSeo->descriptionSeo);
            } else {
                $entity = $this->articleCategoryEntity;
                $entity->setModuleId($values->module);
                $entity->setName($values->name);
                $entity->setUrl($valuesUrl->url);
                $entity->setTitleSeo($valuesSeo->titleSeo);
                $entity->setDescriptionSeo($valuesSeo->descriptionSeo);
                $this->articleCategoryFacadeFactory->create()->save($entity);
            }
            $this->database->commit();
            $this->presenter->flashMessage("Rubrika '{$values->name}' pro jazyk '{$this->languageEntity->getName()}' byla uložena.", "success");
            $this->presenter->redirect("ArticleCategory:edit", ["id" => $entity->getId()]);
        } catch (FacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        }
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->languageEntity = $this->languageEntity;
        $this->template->articleCategoryEntity = $this->articleCategoryEntity;
        $this->template->render();
    }



    /**
     * @return Module[]|array
    */
    protected function getModuleList() : array
    {
        $modules = $this->moduleRepo->findAll();
        return $modules ? Entities::toPair($modules, 'id', 'name') : [];
    }
}