<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\CategoryFiltrationCombinationParameterForm;

use App\Category\CategoryEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupSaveFacadeException;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupSaveFacadeFactory;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterSaveFacadeException;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterSaveFacadeFactory;
use App\Components\SeoFormContainer\IndexFollowForm\IndexFollowForm;
use App\Components\SeoFormContainer\SeoFormContainer;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Database\Context;
use App\NObject;
use Nette\Utils\ArrayHash;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationCombinationParameterFormSuccess extends NObject
{


    /** @var Context */
    protected $database;

    /** @var CategoryFiltrationGroupSaveFacadeFactory */
    protected $categoryFiltrationGroupSaveFacadeFactory;

    /** @var CategoryFiltrationGroupParameterSaveFacadeFactory */
    protected $categoryFiltrationGroupParameterSaveFacadeFactory;



    /**
     * CategoryFiltrationCombinationParameterFormSuccess constructor.
     * @param $context Context
     * @param CategoryFiltrationGroupSaveFacadeFactory $categoryFiltrationGroupSaveFacadeFactory
     * @param $categoryFiltrationGroupParameterSaveFacadeFactory CategoryFiltrationGroupParameterSaveFacadeFactory
     */
    public function __construct(Context $context,
                                CategoryFiltrationGroupSaveFacadeFactory $categoryFiltrationGroupSaveFacadeFactory,
                                CategoryFiltrationGroupParameterSaveFacadeFactory $categoryFiltrationGroupParameterSaveFacadeFactory)
    {
        $this->database = $context;
        $this->categoryFiltrationGroupSaveFacadeFactory = $categoryFiltrationGroupSaveFacadeFactory;
        $this->categoryFiltrationGroupParameterSaveFacadeFactory = $categoryFiltrationGroupParameterSaveFacadeFactory;
    }



    /**
     * @param Form $form
     * @param CategoryFiltrationCombinationParameterForm $component
     */
    public function process(Form $form, CategoryFiltrationCombinationParameterForm $component)
    {
        $values = $form->getValues();
        $presenter = $component->getPresenter();
        $categoryEntity = $component->getCategoryEntity();
        $groupFacade = $this->categoryFiltrationGroupSaveFacadeFactory->create();

        try {
            $this->database->beginTransaction();

            //save group
            if (!$component->getCategoryFiltrationGroupEntity() instanceof CategoryFiltrationGroupEntity) {
                $group = $this->addGroup($categoryEntity, $values);
            } else {
                $group = $this->updateGroup($component->getCategoryFiltrationGroupEntity(), $values);
            }

            //save parameters
            $this->saveParameters($group, $values);

            //save images
			$groupFacade->saveImages($group->getId(), $values->thumbnailImage);

            $this->database->commit();

            $presenter->flashMessage("Kombinace byla uložena.", "success");

            //redirect
            if ($form->isSubmitted() == CategoryFiltrationCombinationParameterForm::SUBMIT_ADD_NEW) {
                $this->redirectAdd($presenter, $categoryEntity);
            }
            $this->redirectEdit($presenter, $group, $categoryEntity);

        } catch (CategoryFiltrationGroupSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), "danger");
        } catch (CategoryFiltrationGroupParameterSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), "danger");
        }
    }



    /**
     * @param CategoryEntity $categoryEntity
     * @param $values ArrayHash
     * @return CategoryFiltrationGroupEntity
     */
    protected function addGroup(CategoryEntity $categoryEntity, ArrayHash $values) : CategoryFiltrationGroupEntity
    {
        $facade = $this->categoryFiltrationGroupSaveFacadeFactory->create();
        $group = $facade->add(
            $categoryEntity,
            $values->description,
            $values->{SeoFormContainer::NAME}->titleSeo,
            $values->{SeoFormContainer::NAME}->descriptionSeo,
            $values->{SeoFormContainer::NAME}->{IndexFollowForm::NAME}->indexSeo === TRUE,
            $values->{SeoFormContainer::NAME}->{IndexFollowForm::NAME}->followSeo === TRUE,
            CategoryFiltrationGroupEntity::PUBLISH,
            $values->showInMenu);

        return $group;
    }



    /**
     * @param CategoryFiltrationGroupEntity $groupEntity
     * @param $values ArrayHash
     * @return CategoryFiltrationGroupEntity
     */
    protected function updateGroup(CategoryFiltrationGroupEntity $groupEntity, ArrayHash $values) : CategoryFiltrationGroupEntity
    {
        //set values
        $groupEntity->setDescription($values->description);
        $groupEntity->setTitleSeo($values->{SeoFormContainer::NAME}->titleSeo);
        $groupEntity->setDescriptionSeo($values->{SeoFormContainer::NAME}->descriptionSeo);
        $groupEntity->setIndexSeo($values->{SeoFormContainer::NAME}->{IndexFollowForm::NAME}->indexSeo === TRUE);
        $groupEntity->setFollowSeo($values->{SeoFormContainer::NAME}->{IndexFollowForm::NAME}->followSeo === TRUE);
        $groupEntity->setStatus(CategoryFiltrationGroupEntity::PUBLISH);
        $groupEntity->setShowInMenu($values->showInMenu);

        $saveFacade = $this->categoryFiltrationGroupSaveFacadeFactory->create();
        $saveFacade->update($groupEntity);

        return $groupEntity;
    }



    /**
     * @param CategoryFiltrationGroupEntity $groupEntity
     * @param ArrayHash $values
     * @return array
     */
    protected function saveParameters(CategoryFiltrationGroupEntity $groupEntity, ArrayHash $values) : array
    {
        $parameterSaveFacade = $this->categoryFiltrationGroupParameterSaveFacadeFactory->create();
        $parameters = $parameterSaveFacade->save($groupEntity, $values->productParameter);

        return $parameters;
    }



    /**
     * @param Presenter $presenter
     * @param CategoryEntity $categoryEntity
     */
    protected function redirectAdd(Presenter $presenter,
                                   CategoryEntity $categoryEntity)
    {
        $presenter->redirect("Category:editFiltrationCombinationAdd", [
            "id" => $categoryEntity->getId()
        ]);
    }



    /**
     * @param Presenter $presenter
     * @param CategoryFiltrationGroupEntity $groupEntity
     * @param CategoryEntity $categoryEntity
     */
    protected function redirectEdit(Presenter $presenter,
                                    CategoryFiltrationGroupEntity $groupEntity,
                                    CategoryEntity $categoryEntity)
    {
        $presenter->redirect("Category:editFiltrationCombinationEdit", [
            "combinationId" => $groupEntity->getId(),
            "id" => $categoryEntity->getId()
        ]);
    }
}