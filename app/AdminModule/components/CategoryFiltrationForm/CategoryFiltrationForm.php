<?php

declare(strict_types = 1);

namespace App\Components\AdminModule\CategoryFiltrationForm;

use App\Category\CategoryEntity;
use App\CategoryFiltration\CategoryFiltrationEntity;
use App\Helpers\Entities;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepositoryFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationForm extends Control
{


    /** @var string */
    const SUBMIT_ADD_NEW = "submitAddNew";

    /** @var ProductParameterGroupTranslationRepositoryFactory */
    protected $productParameterGroupTranslationRepositoryFactory;

    /** @var CategoryFiltrationFormSuccessFactory */
    protected $categoryFiltrationFormSuccessFactory;

    /** @var CategoryEntity|null */
    protected $categoryEntity;

    /** @var CategoryFiltrationEntity|null */
    protected $categoryFiltrationEntity;



    /**
     * CategoryFiltrationForm constructor.
     * @param ProductParameterGroupTranslationRepositoryFactory $productParameterGroupTranslationRepositoryFactory
     * @param CategoryFiltrationFormSuccessFactory $categoryFiltrationFormSuccessFactory
     */
    public function __construct(ProductParameterGroupTranslationRepositoryFactory $productParameterGroupTranslationRepositoryFactory,
                                CategoryFiltrationFormSuccessFactory $categoryFiltrationFormSuccessFactory)
    {
        $this->productParameterGroupTranslationRepositoryFactory = $productParameterGroupTranslationRepositoryFactory;
        $this->categoryFiltrationFormSuccessFactory = $categoryFiltrationFormSuccessFactory;
    }



    /**
     * @param CategoryEntity $categoryEntity
     * @return CategoryFiltrationForm
     */
    public function setCategoryEntity(CategoryEntity $categoryEntity) : self
    {
        $this->categoryEntity = $categoryEntity;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getCategoryEntity()
    {
        return $this->categoryEntity;
    }



    /**
     * @return mixed
     */
    public function getCategoryFiltrationEntity()
    {
        return $this->categoryFiltrationEntity;
    }



    /**
     * @param CategoryFiltrationEntity $categoryFiltrationEntity
     * @return self
     */
    public function setCategoryFiltrationEntity(CategoryFiltrationEntity $categoryFiltrationEntity) : self
    {
        $this->categoryFiltrationEntity = $categoryFiltrationEntity;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        //get default lang
        $localeResolver = new LocalizationResolver();
        $defaultLanguage = $localeResolver->getDefault();

        //get list of group
        $groupRepo = $this->productParameterGroupTranslationRepositoryFactory->create();
        $groups = $groupRepo->findByLanguageId($defaultLanguage->getId());
        $groupsList = $groups ? Entities::toPair($groups, "productParameterGroupId", "name") : [];

        //form
        $form = new Form();
        $form->addSelect("groupId", "Skupina parametrů*", $groupsList)
            ->setPrompt('- Vyberte -')
            ->setAttribute("class", "form-control")
            ->setRequired("Zvolte skupinu.")
            ->setDisabled($this->categoryFiltrationEntity instanceof CategoryFiltrationEntity ? TRUE : FALSE);
        $form->addSubmit("submit", "Přidat")
            ->setAttribute("class", "btn btn-success");
        $form->addSubmit(self::SUBMIT_ADD_NEW, "Uložit a přidat další")
            ->setAttribute("class", "btn btn-success");
        $this->setDefaultValues($form);
        $form->onSuccess[] = [$this, "formSuccess"];
        return $form;
    }



    /**
     * @param Form $form
     * @return Form
     */
    protected function setDefaultValues(Form $form) : Form
    {
        if ($this->categoryFiltrationEntity instanceof CategoryFiltrationEntity) {
            $form->setDefaults([
                "groupId" => $this->categoryFiltrationEntity->getProductParameterGroupId()
            ]);
        }
        return $form;
    }



    /**
     * @param Form $form
     */
    public function formSuccess(Form $form)
    {
        $formSuccess = $this->categoryFiltrationFormSuccessFactory->create();
        $formSuccess->process($form, $this);
    }



    /**
     * @throws CategoryFiltrationFormException
     */
    public function render()
    {
        if (!$this->categoryEntity instanceof CategoryEntity) {
            throw new CategoryFiltrationFormException(sprintf("You must set '%s' object.",
                CategoryEntity::class));
        }

        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }
}