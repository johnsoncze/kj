<?php

declare(strict_types = 1);

namespace App\Components\CategoryForm;

use App\Category\CategoryEntity;
use App\Category\CategoryRepositoryFactory;
use App\Category\CategorySaveFacadeException;
use App\Category\CategorySaveFacadeFactory;
use App\Components\OgFormContainer\OgFormContainer;
use App\Components\OgFormContainer\OgFormContainerFactory;
use App\Components\RelatedPageContainer\RelatedPageContainer;
use App\Components\RelatedPageContainer\RelatedPageContainerFactory;
use App\Components\CollectionFormContainer\CollectionFormContainer;
use App\Components\CollectionFormContainer\CollectionFormContainerFactory;
use App\Components\SeoFormContainer\SeoFormContainer;
use App\Components\SeoFormContainer\SeoFormContainerFactory;
use App\Components\UrlFormContainer\UrlFormContainer;
use App\Components\UrlFormContainer\UrlFormContainerFactory;
use App\PromoArticle\PromoArticleRepository;
use App\PromoArticle\PromoArticleRepositoryFactory;
use App\Helpers\Arrays;
use App\Helpers\Entities;
use App\Helpers\Images;
use App\Helpers\Summernote;
use App\Language\LanguageEntity;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryForm extends Control
{


	/** @var CategoryRepositoryFactory */
	protected $categoryRepositoryFactory;

	/** @var CategorySaveFacadeFactory */
	protected $categorySaveFacadeFactory;

	/** @var SeoFormContainerFactory */
	protected $seoFormContainerFactory;

	/** @var UrlFormContainerFactory */
	protected $urlFormContainerFactory;

	/** @var RelatedPageContainerFactory */
	protected $relatedPageContainerFactory;

	/** @var CollectionFormContainerFactory */
	protected $collectionFormContainerFactory;

	/** @var PromoArticleRepositoryFactory */
	protected $promoArticleRepositoryFactory;
	
	/** @var LanguageEntity|null */
	protected $languageEntity;

	/** @var CategoryEntity|null */
	protected $categoryEntity;

	/** @var CategoryFormProcessSuccessFactory */
	protected $categoryFormProcessSuccessFactory;
    protected $ogFormContainerFactory;


    public function __construct(
        CategoryRepositoryFactory $categoryRepositoryFactory,
        CategorySaveFacadeFactory $categorySaveFacadeFactory,
        SeoFormContainerFactory $seoFormContainerFactory,
        UrlFormContainerFactory $urlFormContainerFactory,
        CategoryFormProcessSuccessFactory $categoryFormProcessSuccessFactory,
        OgFormContainerFactory $ogFormContainerFactory,
        RelatedPageContainerFactory $relatedPageContainerFactory,
        CollectionFormContainerFactory $collectionFormContainerFactory,
        PromoArticleRepositoryFactory $promoArticleRepositoryFactory
    ) {
        $this->ogFormContainerFactory = $ogFormContainerFactory;
        parent::__construct();
        $this->categoryRepositoryFactory = $categoryRepositoryFactory;
        $this->categorySaveFacadeFactory = $categorySaveFacadeFactory;
        $this->seoFormContainerFactory = $seoFormContainerFactory;
        $this->urlFormContainerFactory = $urlFormContainerFactory;
        $this->categoryFormProcessSuccessFactory = $categoryFormProcessSuccessFactory;
        $this->relatedPageContainerFactory = $relatedPageContainerFactory;
        $this->collectionFormContainerFactory = $collectionFormContainerFactory;
				$this->promoArticleRepositoryFactory = $promoArticleRepositoryFactory;
    }



	/**
	 * @param CategoryEntity $categoryEntity
	 * @return self
	 */
	public function setCategoryEntity(CategoryEntity $categoryEntity) : self
	{
		$this->categoryEntity = $categoryEntity;
		return $this;
	}



	/**
	 * @return CategoryEntity|null
	 */
	public function getCategoryEntity()
	{
		return $this->categoryEntity;
	}



	/**
	 * @param $languageEntity LanguageEntity
	 * @return self
	 */
	public function setLanguageEntity(LanguageEntity $languageEntity) : self
	{
		$this->languageEntity = $languageEntity;
		return $this;
	}



	/**
	 * @return LanguageEntity|null
	 */
	public function getLanguageEntity()
	{
		return $this->languageEntity;
	}



	/**
	 * @return Form
	 */
	public function createComponentForm() : Form
	{
		$imageTemplateList = CategoryEntity::getImageTemplateList();
		$templateList = CategoryEntity::getTemplateList();

		//Create form
		$form = new Form();
		$form->addText("language", "Jazyk")
			->setAttribute("class", "form-control")
			->setDisabled(TRUE)
			->setDefaultValue($this->languageEntity->getName());
		$form->addCheckbox('showOnHomepage', ' Zobrazit na hlavní stránce');
		$form->addCheckbox('top', ' Top');
		$form->addText("name", "Název")
			->setRequired("Vyplňte název kategorie")
			->setAttribute("class", "form-control")
			->setAttribute('autofocus');
		$form->addSelect("status", "Stav", Arrays::toPair(CategoryEntity::getStatuses(), "key", "translate"))
			->setAttribute("class", "form-control");
		$form->addSelect("parentCategoryId", "Nadřazená kategorie", $this->getCategoryList())
			->setAttribute("class", "form-control")
			->setPrompt("- vyberte -");
		$form->addSelect('template', 'Grafická šablona (V případě, že nevyberete žádnou, bude použita výchozí)', $templateList)
			->setAttribute('class', 'form-control')
			->setPrompt('- vyberte -');
		$form->addSelect('imageTemplate', 'Image šablona', $imageTemplateList)
			->setAttribute('class', 'form-control')
			->setPrompt('- vyberte -');
		$form->addTextArea("content", "Obsah")
			->setAttribute("class", "form-control")
			->setHtmlId('ckEditor')
            ->setEmptyValue(Summernote::EMPTY_STRING_VALUE);
		$form->addTextArea("description", "Produktový popis")
			->setAttribute("class", "form-control");
//			->setHtmlId('ckEditor')
//            ->setEmptyValue(Summernote::EMPTY_STRING_VALUE);
		$form->addSelect("promoArticleId1", "Článek pod výpisem produktů 1-3", $this->getPromoArticleList())
			->setAttribute("class", "form-control")
			->setPrompt("- žádný článek -");
		$form->addSelect("promoArticleId2", "", $this->getPromoArticleList())
			->setAttribute("class", "form-control")
			->setPrompt("- žádný článek -");
		$form->addSelect("promoArticleId3", "", $this->getPromoArticleList())
			->setAttribute("class", "form-control")
			->setPrompt("- žádný článek -");

		
		//images
		$form->addUpload('generalImage', 'Úvodní fotografie')
			->setRequired(FALSE)
			->addRule(Form::MIME_TYPE, 'Nahrávejte pouze obrázek.', Images::getMimeTypes());
		$form->addUpload('thumbnailImage', 'Miniatura')
			->setRequired(FALSE)
			->addRule(Form::MIME_TYPE, 'Nahrávejte pouze obrázek.', Images::getMimeTypes());
		$form->addUpload('generalImageDesktop', 'Úvodní fotografie desktop')
			->setRequired(FALSE)
			->addRule(Form::MIME_TYPE, 'Nahrávejte pouze obrázek.', Images::getMimeTypes());
		$form->addUpload('generalImageMobile', 'Úvodní fotografie mobilní')
			->setRequired(FALSE)
			->addRule(Form::MIME_TYPE, 'Nahrávejte pouze obrázek.', Images::getMimeTypes());
		$form->addUpload('subcategoryImage', 'Obrázek jako subkategorie')
			->setRequired(FALSE)
			->addRule(Form::MIME_TYPE, 'Nahrávejte pouze obrázek.', Images::getMimeTypes());

		//Add components
		$form->addComponent($this->relatedPageContainerFactory->create(), RelatedPageContainer::NAME);
		$form->addComponent($this->urlFormContainerFactory->create(), UrlFormContainer::NAME);
		$form->addComponent($this->seoFormContainerFactory->create(), SeoFormContainer::NAME);
		$form->addComponent($this->ogFormContainerFactory->create(), OgFormContainer::NAME);

		$collectionForm = $this->collectionFormContainerFactory->create();
		$collectionForm->categoryEntity = $this->categoryEntity;
		$form->addComponent($collectionForm, CollectionFormContainer::NAME);
		
		
		//Set default values
		$this->setFormDefaultValues($form, $templateList, $imageTemplateList);

		$form->addSubmit("submit", "Uložit")
			->setAttribute("class", "btn btn-success");
		$form->onSuccess[] = [$this, "formSuccess"];
		return $form;
	}



	/**
	 * @param Form $form
	 */
	public function formSuccess(Form $form)
	{
		$process = $this->categoryFormProcessSuccessFactory->create();
		$process->process($this, $form);
	}



	/**
	 * @param Form $form
	 * @param $templateList array
	 * @param $imageTemplateList array
	 * @return Form
	 */
	protected function setFormDefaultValues(Form $form, array $templateList = [], array $imageTemplateList = []) : Form
	{
		if ($this->categoryEntity instanceof CategoryEntity) {

			$form->setDefaults([
				'showOnHomepage' => $this->categoryEntity->getShowOnHomepage(),
				'top' => $this->categoryEntity->getTop(),
				"name" => $this->categoryEntity->getName(),
				"status" => $this->categoryEntity->getStatus(),
				"parentCategoryId" => $this->categoryEntity->getParentCategoryId(),
				"promoArticleId1" => $this->categoryEntity->getPromoArticleId1(),
				"promoArticleId2" => $this->categoryEntity->getPromoArticleId2(),
				"promoArticleId3" => $this->categoryEntity->getPromoArticleId3(),
				'template' => $this->categoryEntity->getTemplate() && array_key_exists($this->categoryEntity->getTemplate(), $templateList) ? $this->categoryEntity->getTemplate() : NULL,
				'imageTemplate' => $this->categoryEntity->getImageTemplate() && array_key_exists($this->categoryEntity->getImageTemplate(), $imageTemplateList) ? $this->categoryEntity->getImageTemplate() : NULL,
				UrlFormContainer::NAME => [
					"url" => $this->categoryEntity->getUrl()
				], "content" => $this->categoryEntity->getContent(), "description" => $this->categoryEntity->getDescription(),
				SeoFormContainer::NAME => [
					"titleSeo" => $this->categoryEntity->getTitleSeo(),
					"descriptionSeo" => $this->categoryEntity->getDescriptionSeo()
				],
				OgFormContainer::NAME => [
						"titleOg" => $this->categoryEntity->getTitleOg(),
						"descriptionOg" => $this->categoryEntity->getDescriptionOg(),
				],
				RelatedPageContainer::NAME => [
						"relatedPageText" => $this->categoryEntity->getRelatedPageText(),
						"relatedPageScrolledText" => $this->categoryEntity->getRelatedPageScrolledText(),
						"relatedPageLink" => $this->categoryEntity->getRelatedPageLink(),
				],
				CollectionFormContainer::NAME => [
						"collectionSubname" => $this->categoryEntity->getCollectionSubname(),
						"collectionPerex" => $this->categoryEntity->getCollectionPerex(),
						"collectionText" => $this->categoryEntity->getCollectionText(),
						"collectionImage" => $this->categoryEntity->getCollectionImage(),
				],					
			]);
		}
		return $form;
	}



	/**
	 * @return array
	 */
	protected function getCategoryList() : array
	{
		//params
		$languageId = $this->languageEntity->getId();
		$categoryId = $this->categoryEntity ? $this->categoryEntity->getId() : NULL;

		//Load
		$repo = $this->categoryRepositoryFactory->create();
		$categories = $repo->findByLanguageIdWithoutCategoryId($languageId, $categoryId);

		return $categories ? Entities::toPair($categories, "id", "name") : [];
	}

	
	/**
	 * @return array
	 */
	protected function getPromoArticleList() : array
	{
		$repo = $this->promoArticleRepositoryFactory->create();
		$promoArticles = $repo->findAll();

		return $promoArticles ? Entities::toPair($promoArticles, "id", "title") : [];
	}	

	

	public function render()
	{
		if (!$this->languageEntity instanceof LanguageEntity) {
			throw new CategoryFormException(sprintf("You must set '%s' object.",
				LanguageEntity::class));
		}

		//vars
		$this->template->categoryEntity = $this->categoryEntity;

		//renders
		$this->template->setFile(__DIR__ . "/default.latte");
		$this->template->render();
	}



	/**
	 * @param $id int category id
	 */
	public function handleDeleteGeneralImage(int $id)
	{
		try {
			$categorySaveFacade = $this->categorySaveFacadeFactory->create();
			$categorySaveFacade->deleteGeneralImage($id);
			$this->getPresenter()->sendJson(['code' => 0]);
		} catch (CategorySaveFacadeException $exception) {
			$this->getPresenter()->sendJson(['code' => 100, 'message' => $exception->getMessage()]);
		}
	}

	
	/**
	 * @param $id int category id
	 */
	public function handleDeleteGeneralImageDesktop(int $id)
	{
		try {
			$categorySaveFacade = $this->categorySaveFacadeFactory->create();
			$categorySaveFacade->deleteGeneralImageDesktop($id);
			$this->getPresenter()->sendJson(['code' => 0]);
		} catch (CategorySaveFacadeException $exception) {
			$this->getPresenter()->sendJson(['code' => 100, 'message' => $exception->getMessage()]);
		}
	}

	
	/**
	 * @param $id int category id
	 */
	public function handleDeleteGeneralImageMobile(int $id)
	{
		try {
			$categorySaveFacade = $this->categorySaveFacadeFactory->create();
			$categorySaveFacade->deleteGeneralImageMobile($id);
			$this->getPresenter()->sendJson(['code' => 0]);
		} catch (CategorySaveFacadeException $exception) {
			$this->getPresenter()->sendJson(['code' => 100, 'message' => $exception->getMessage()]);
		}
	}

	/**
	 * @param $id int category id
	 */
	public function handleDeleteCollectionImage(int $id)
	{
		try {
			$categorySaveFacade = $this->categorySaveFacadeFactory->create();
			$categorySaveFacade->deleteCollectionImage($id);
			$this->getPresenter()->sendJson(['code' => 0]);
		} catch (CategorySaveFacadeException $exception) {
			$this->getPresenter()->sendJson(['code' => 100, 'message' => $exception->getMessage()]);
		}
	}	
	
	
	/**
	 * @param $id int category id
	 */
	public function handleDeleteSubcategoryImage(int $id)
	{
		try {
			$categorySaveFacade = $this->categorySaveFacadeFactory->create();
			$categorySaveFacade->deleteSubcategoryImage($id);
			$this->getPresenter()->sendJson(['code' => 0]);
		} catch (CategorySaveFacadeException $exception) {
			$this->getPresenter()->sendJson(['code' => 100, 'message' => $exception->getMessage()]);
		}
	}	
	
	
	
	/**
	 * @param $id int category id
	 */
	public function handleDeleteThumbnailImage(int $id)
	{
		try {
			$categorySaveFacade = $this->categorySaveFacadeFactory->create();
			$categorySaveFacade->deleteThumbnailImage($id);
			$this->getPresenter()->sendJson(['code' => 0]);
		} catch (CategorySaveFacadeException $exception) {
			$this->getPresenter()->sendJson(['code' => 100, 'message' => $exception->getMessage()]);
		}
	}
}
