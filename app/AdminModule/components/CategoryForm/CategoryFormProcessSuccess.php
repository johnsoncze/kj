<?php

declare(strict_types = 1);

namespace App\Components\CategoryForm;

use App\Category\CategoryEntity;
use App\Category\CategorySaveFacade;
use App\Category\CategorySaveFacadeException;
use App\Category\CategorySaveFacadeFactory;
use App\CategoryProductParameter\CategoryProductParameterSaveFacadeException;
use App\CategoryProductParameter\CategoryProductParameterSaveFacadeFactory;
use App\Components\OgFormContainer\OgFormContainer;
use App\Components\RelatedPageContainer\RelatedPageContainer;
use App\Components\CollectionFormContainer\CollectionFormContainer;
use App\Components\SeoFormContainer\SeoFormContainer;
use App\Components\UrlFormContainer\UrlFormContainer;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use App\NObject;
use Nette\Utils\ArrayHash;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFormProcessSuccess extends NObject
{


    /** @var Context */
    protected $database;

    /** @var CategorySaveFacadeFactory */
    protected $categorySaveFacadeFactory;

    /** @var CategoryProductParameterSaveFacadeFactory */
    protected $categoryProductParameterSaveFacadeFactory;

    /** @var CategoryForm */
    protected $categoryForm;



    /**
     * CategoryFormProcessSuccess constructor.
     * @param $database
     * @param CategorySaveFacadeFactory $categorySaveFacadeFactory
     * @param CategoryProductParameterSaveFacadeFactory $categoryProductParameterSaveFacadeFactory
     */
    public function __construct(Context $database,
                                CategorySaveFacadeFactory $categorySaveFacadeFactory,
                                CategoryProductParameterSaveFacadeFactory $categoryProductParameterSaveFacadeFactory)
    {
        $this->database = $database;
        $this->categorySaveFacadeFactory = $categorySaveFacadeFactory;
        $this->categoryProductParameterSaveFacadeFactory = $categoryProductParameterSaveFacadeFactory;
    }



    /**
     * @param CategoryForm $categoryForm
     * @param Form $form
     */
    public function process(CategoryForm $categoryForm, Form $form)
    {
        $this->categoryForm = $categoryForm;
        $values = $form->getValues();
        $categorySaveFacade = $this->categorySaveFacadeFactory->create();

				try {
            $this->database->beginTransaction();
            if ($category = $this->categoryForm->getCategoryEntity()) {
                $category = $this->update($category, $form);
            } else {
                $category = $this->add($form);
            }
						
            $this->saveImages($category, $values, $categorySaveFacade);
            $this->database->commit();
            $this->categoryForm->getPresenter()->flashMessage(sprintf("Kategorie '%s' byla uložena.",
                $category->getName()), "success");

            $parameters = ["id" => $category->getId()];
            $this->categoryForm->getPresenter()->redirect("Category:edit", $parameters);
        } catch (CategorySaveFacadeException $exception) {
            $this->database->rollBack();
            $this->categoryForm->getPresenter()->flashMessage($exception->getMessage(), "danger");
        } catch (CategoryProductParameterSaveFacadeException $exception) {
            $this->database->rollBack();
            $this->categoryForm->getPresenter()->flashMessage($exception->getMessage(), "danger");
        }
    }



    /**
     * @param Form $form
     * @return CategoryEntity
     */
    protected function add(Form $form)
    {
        $values = $form->getValues();

        //Save category
        $facade = $this->categorySaveFacadeFactory->create();
        $category = $facade->add($this->categoryForm->getLanguageEntity()->getId(),
            ($values->parentCategoryId ?: NULL),
            $values->name,
            $values->content,
            $values->{UrlFormContainer::NAME}->url,
            $values->{SeoFormContainer::NAME}->titleSeo,
            $values->{SeoFormContainer::NAME}->descriptionSeo,
            NULL,
            $values->status,
            $values->template,
            $values->showOnHomepage,
            FALSE,
            $values->imageTemplate,
            $values->top);

        return $category;
    }



    /**
     * @param CategoryEntity $categoryEntity
     * @param Form $form
     * @return CategoryEntity
     */
    protected function update(CategoryEntity $categoryEntity, Form $form) : CategoryEntity
    {
        $values = $form->getValues();

        //set values
        $categoryEntity->setParentCategoryId($values->parentCategoryId);
        $categoryEntity->setName($values->name);
        $categoryEntity->setContent($values->content);
        $categoryEntity->setDescription($values->description);
        $categoryEntity->setUrl($values->{UrlFormContainer::NAME}->url);
        $categoryEntity->setTitleSeo($values->{SeoFormContainer::NAME}->titleSeo);
        $categoryEntity->setDescriptionSeo($values->{SeoFormContainer::NAME}->descriptionSeo);
        $categoryEntity->setStatus($values->status);
        $categoryEntity->setTemplate($values->template);
        $categoryEntity->setShowOnHomepage($values->showOnHomepage);
        $categoryEntity->setImageTemplate($values->imageTemplate);
        $categoryEntity->setTop($values->top);
        $categoryEntity->setTitleOg($values->{OgFormContainer::NAME}->titleOg);
        $categoryEntity->setDescriptionOg($values->{OgFormContainer::NAME}->descriptionOg);
        $categoryEntity->setRelatedPageText($values->{RelatedPageContainer::NAME}->relatedPageText);
        $categoryEntity->setRelatedPageScrolledText($values->{RelatedPageContainer::NAME}->relatedPageScrolledText);
        $categoryEntity->setRelatedPageLink($values->{RelatedPageContainer::NAME}->relatedPageLink);
        $categoryEntity->setPromoArticleId1($values->promoArticleId1);
        $categoryEntity->setPromoArticleId2($values->promoArticleId2);
        $categoryEntity->setPromoArticleId3($values->promoArticleId3);
        $categoryEntity->setCollectionSubname($values->{CollectionFormContainer::NAME}->collectionSubname);
        $categoryEntity->setCollectionPerex($values->{CollectionFormContainer::NAME}->collectionPerex);
        $categoryEntity->setCollectionText($values->{CollectionFormContainer::NAME}->collectionText);


        //save category
        $facade = $this->categorySaveFacadeFactory->create();
        $facade->update($categoryEntity);

        return $categoryEntity;
    }



    /**
	 * @param $category CategoryEntity
	 * @param $values ArrayHash
	 * @param $saveFacade CategorySaveFacade
	 * @return CategoryEntity
	 * @throws CategorySaveFacadeException
    */
    protected function saveImages(CategoryEntity $category, ArrayHash $values, CategorySaveFacade $saveFacade) : CategoryEntity
	{			
		$saveFacade->saveImages($category->getId(), $values->generalImage, $values->thumbnailImage, 
														$values->generalImageDesktop, $values->generalImageMobile,
														$values->collectionForm->collectionImage, $values->subcategoryImage);
		return $category;
	}
}