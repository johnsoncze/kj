<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\CategoryFiltrationCombinationParameterForm;

use App\Category\CategoryEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupSaveFacadeException;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupSaveFacadeFactory;
use App\Components\SeoFormContainer\IndexFollowForm\IndexFollowForm;
use App\Components\SeoFormContainer\IndexFollowForm\IndexFollowFormFactory;
use App\Components\SeoFormContainer\SeoFormContainer;
use App\Components\SeoFormContainer\SeoFormContainerFactory;
use App\Helpers\Entities;
use App\Helpers\Images;
use App\Helpers\Summernote;
use App\ProductParameter\ProductParameterFindFacadeFactory;
use App\ProductParameter\ProductParameterTranslationRepositoryFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationCombinationParameterForm extends Control
{


    /** @var string name of button for submit form and add a new combination */
    const SUBMIT_ADD_NEW = "submitAddNew";

    /** @var ProductParameterFindFacadeFactory */
    protected $productParameterFindFacadeFactory;

    /** @var SeoFormContainerFactory */
    protected $seoFormContainerFactory;

    /** @var IndexFollowFormFactory */
    protected $indexFollowFormFactory;

    /** @var CategoryFiltrationCombinationParameterFormSuccessFactory */
    protected $categoryFiltrationCombinationParameterFormSuccessFactory;

    /** @var CategoryEntity|null */
    protected $categoryEntity;

    /** @var CategoryFiltrationGroupEntity */
    protected $categoryFiltrationGroupEntity;

    /** @var CategoryFiltrationGroupSaveFacadeFactory */
    protected $groupSaveFacadeFactory;

    /** @var LocalizationResolver */
    protected $localizationResolver;

    /** @var ProductParameterTranslationRepositoryFactory */
    protected $productParameterTranslationRepoFactory;



    public function __construct(CategoryFiltrationGroupSaveFacadeFactory $categoryFiltrationGroupSaveFacadeFactory,
								ProductParameterFindFacadeFactory $productParameterFindFacadeFactory,
                                SeoFormContainerFactory $seoFormContainerFactory,
                                IndexFollowFormFactory $indexFollowFormFactory,
                                CategoryFiltrationCombinationParameterFormSuccessFactory $categoryFiltrationCombinationParameterFormSuccessFactory,
                                ProductParameterTranslationRepositoryFactory $productParameterTranslationRepositoryFactory)
    {
        parent::__construct();
        $this->groupSaveFacadeFactory = $categoryFiltrationGroupSaveFacadeFactory;
        $this->productParameterFindFacadeFactory = $productParameterFindFacadeFactory;
        $this->seoFormContainerFactory = $seoFormContainerFactory;
        $this->indexFollowFormFactory = $indexFollowFormFactory;
        $this->categoryFiltrationCombinationParameterFormSuccessFactory = $categoryFiltrationCombinationParameterFormSuccessFactory;
        $this->productParameterTranslationRepoFactory = $productParameterTranslationRepositoryFactory;
        $this->localizationResolver = new LocalizationResolver();
    }



    /**
     * @param CategoryEntity $categoryEntity
     * @return CategoryFiltrationCombinationParameterForm
     */
    public function setCategoryEntity(CategoryEntity $categoryEntity) : self
    {
        $this->categoryEntity = $categoryEntity;
        return $this;
    }



    /**
     * @return CategoryEntity
     * @throws CategoryFiltrationCombinationParameterFormException
     */
    public function getCategoryEntity() : CategoryEntity
    {
        if (!$this->categoryEntity instanceof CategoryEntity) {
            throw new CategoryFiltrationCombinationParameterFormException(sprintf("You must set object '%s'.", CategoryEntity::class));
        }
        return $this->categoryEntity;
    }



    /**
     * @param CategoryFiltrationGroupEntity $categoryFiltrationGroupEntity
     * @return $this
     */
    public function setCategoryFiltrationGroupEntity(CategoryFiltrationGroupEntity $categoryFiltrationGroupEntity)
    {
        $this->categoryFiltrationGroupEntity = $categoryFiltrationGroupEntity;
        return $this;
    }



    /**
     * @return CategoryFiltrationGroupEntity|null
     */
    public function getCategoryFiltrationGroupEntity()
    {
        return $this->categoryFiltrationGroupEntity;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $form = new Form();
        $form->addCheckbox('showInMenu', ' Zobrazit v menu');
        $form->addMultiSelect("productParameter", "Parametry filtrace*", $this->getParameterList())
            ->setAttribute("class", "form-control select2")
            ->setRequired("Zvolte parametry.");
        $form->addTextArea("description", "Popis kategorie")
            ->setAttribute("class", "form-control")
			->setHtmlId('ckEditor')
            ->setEmptyValue(Summernote::EMPTY_STRING_VALUE);

        //images
		$form->addUpload('thumbnailImage', 'Miniatura')
			->setRequired(FALSE)
			->addRule(Form::MIME_TYPE, 'Nahrávejte pouze obrázek.', Images::getMimeTypes());

        //Components
        $seoForm = $this->seoFormContainerFactory->create();
        $titleSeo = $seoForm->getComponent('titleSeo');
        $titleSeo->setRequired('Vyplňte titulek');
        $seoForm->addComponent($this->indexFollowFormFactory->create(), IndexFollowForm::NAME);
        $form->addComponent($seoForm, SeoFormContainer::NAME);

        //set default values
        $this->setDefaultValues($form);

        $form->addSubmit("submit", "Uložit")
            ->setAttribute("class", "btn btn-success");
        $form->addSubmit(self::SUBMIT_ADD_NEW, "Uložit a přidat další");
        $form->onSuccess[] = function ($form) {
            $formSuccess = $this->categoryFiltrationCombinationParameterFormSuccessFactory->create();
            $formSuccess->process($form, $this);
        };
        return $form;
    }



    /**
     * @param Form $form
     * @return Form
     */
    protected function setDefaultValues(Form $form) : Form
    {
        if ($group = $this->getCategoryFiltrationGroupEntity()) {
            $form->setDefaults([
                'showInMenu' => $group->getShowInMenu(),
                "productParameter" => Entities::getProperty($group->getParameters(), "productParameterId"),
                "description" => $group->getDescription(),
                SeoFormContainer::NAME => [
                    "titleSeo" => $group->getTitleSeo(),
                    "descriptionSeo" => $group->getDescriptionSeo(),
                    IndexFollowForm::NAME => [
                        "indexSeo" => $group->getIndexSeo(),
                        "followSeo" => $group->getFollowSeo()
                    ]
                ]
            ]);
        }

        return $form;
    }



    public function render()
    {
    	$this->template->category = $this->categoryEntity;
        $this->template->categoryFiltrationGroup = $this->getCategoryFiltrationGroupEntity();
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }



    /**
	 * @param $id int group id
    */
	public function handleDeleteThumbnailImage(int $id)
	{
		$presenter = $this->getPresenter();

		try {
			$groupSaveFacade = $this->groupSaveFacadeFactory->create();
			$groupSaveFacade->deleteThumbnail($id);
			$presenter->sendJson(['code' => 0]);
		} catch (CategoryFiltrationGroupSaveFacadeException $exception) {
			$presenter->sendJson(['code' => 100, 'message' => $exception->getMessage()]);
		}
	}



    /**
     * @return array
     */
    protected function getParameterList() : array
    {
        $categoryEntity = $this->getCategoryEntity();
        $defaultLanguage = $this->localizationResolver->getDefault();
        $parameterRepo = $this->productParameterTranslationRepoFactory->create();
        return $parameterRepo->findListByCategoryIdAndLanguageId($categoryEntity->getId(), $defaultLanguage->getId());
    }
}