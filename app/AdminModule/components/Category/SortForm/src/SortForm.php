<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\SortForm;

use App\AdminModule\Components\Category\SortForm\Resolver\CollectionSliderResolver;
use App\AdminModule\Components\Category\SortForm\Resolver\FirstLevelResolver;
use App\AdminModule\Components\Category\SortForm\Resolver\HomepageListResolver;
use App\AdminModule\Components\Category\SortForm\Resolver\ResolverList;
use App\Category\CategoryRepository;
use App\Category\CategorySaveFacadeFactory;
use App\Components\SortForm\SortFormFactory;
use App\Helpers\Entities;
use App\Language\LanguageEntity;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SortForm extends Control
{


    /** @var string url parameter */
    const CATEGORY_PARENT_ID = 'categoryParentId';

    /** @var CategorySaveFacadeFactory */
    private $categoryFacadeFactory;

    /** @var CategoryRepository */
    private $categoryRepo;

    /** @var Context */
    private $database;

    /** @var LanguageEntity|null */
    private $language;

    /** @var ResolverList */
    private $resolverList;

    /** @var SortFormFactory */
    private $sortFormFactory;



    public function __construct(CategorySaveFacadeFactory $categorySaveFacadeFactory,
                                CategoryRepository $categoryRepo,
                                Context $context,
                                ResolverList $resolverList,
                                SortFormFactory $sortFormFactory)
    {
        parent::__construct();
        $this->categoryFacadeFactory = $categorySaveFacadeFactory;
        $this->categoryRepo = $categoryRepo;
        $this->database = $context;
        $this->resolverList = $resolverList;
        $this->sortFormFactory = $sortFormFactory;
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



    /**
     * @return \App\Components\SortForm\SortForm
     */
    public function createComponentSortForm() : \App\Components\SortForm\SortForm
    {
        $source = $this->getPresenter()->getParameter(self::CATEGORY_PARENT_ID);
        $categories = $this->resolverList->findItems($source, $this->language);
        $categories = $categories ? Entities::toPair($categories, 'id', 'name') : [];

        $sortForm = $this->sortFormFactory->create();
        $sortForm->setItems($categories);
        $sortForm->setOnSuccess(function (Form $form, array $sorting) use ($source) {
            $sorting = array_flip($sorting);
            $presenter = $this->getPresenter();
            $this->database->beginTransaction();
            $this->resolverList->save($source, $sorting);
            $this->database->commit();

            $presenter->flashMessage('Řazení bylo uloženo.', 'success');
            $presenter->redirect('this');
        });
        return $sortForm;
    }



    /**
     * @return Form
     * @throws AbortException
     */
    public function createComponentCategoryForm() : Form
    {
        $categories = $this->categoryRepo->findByLanguageIdSortedByName($this->language->getId());
        $categories = $categories ? Entities::toPair($categories, 'id', 'name') : [];

        $categoryList['Komponenty'] = [HomepageListResolver::KEY => 'Výpis na hlavní stránce', CollectionSliderResolver::KEY => 'Výpis kolekcí na hlavní stránce'];
        $categoryList['Úroveň'] = [FirstLevelResolver::KEY => 'První úroveň'];
        $categoryList['Uživatelské kategorie'] = $categories;

        $form = new Form();
        $form->addSelect('categoryParentId', 'Kategorie', $categoryList)
            ->setAttribute('class', 'form-control')
            ->setDefaultValue($this->getPresenter()->getParameter(self::CATEGORY_PARENT_ID));
        $form->addSubmit('submit', 'Přejít')
            ->setAttribute('class', 'btn btn-default')
            ->setAttribute('style', 'margin-top:-3px');
        $form->onSuccess[] = function (Form $form) {
            $values = $form->getValues();
            $params['languageId'] = $this->language->getId();
            if ($values->categoryParentId) {
                $params[self::CATEGORY_PARENT_ID] = $values->categoryParentId;
            }
            $this->getPresenter()->redirect('Category:sort', $params);
        };
        return $form;
    }



    public function render()
    {
        $this->template->language = $this->language;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}