<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\ProductParameterGroupLockList\ProductParameterGroupLockList;
use App\AdminModule\Components\ProductParameterGroupLockList\ProductParameterGroupLockListFactory;
use App\Components\ProductParameterGroupForm\ProductParameterGroupForm;
use App\Components\ProductParameterGroupForm\ProductParameterGroupFormFactory;
use App\Components\ProductParameterGroupList\ProductParameterGroupList;
use App\Components\ProductParameterGroupList\ProductParameterGroupListFactory;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRemoveFacadeException;
use App\ProductParameterGroup\ProductParameterGroupRemoveFacadeFactory;
use App\ProductParameterGroup\ProductParameterGroupRepositoryFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupPresenter extends AdminModulePresenter
{


    /** @var ProductParameterGroupLockListFactory @inject */
    public $productParameterGroupLockListFactory;

    /** @var ProductParameterGroupFormFactory @inject */
    public $productParameterGroupFormFactory;

    /** @var ProductParameterGroupListFactory @inject */
    public $productParameterGroupListFactory;

    /** @var ProductParameterGroupRepositoryFactory @inject */
    public $productParameterGroupRepositoryFactory;

    /** @var ProductParameterGroupRemoveFacadeFactory @inject */
    public $productParameterGroupRemoveFacadeFactory;

    /** @var ProductParameterGroupEntity|null */
    public $productParameterGroupEntity;



    /**
     * @param int $id
     */
    public function actionEdit(int $id)
    {
        $this->productParameterGroupEntity = $this->checkRequest((int)$id, ProductParameterGroupRepositoryFactory::class);
        $this->template->group = $this->productParameterGroupEntity;
        $this->template->setFile(__DIR__ . '/templates/ProductParameterGroup/add.latte');
    }



    /**
     * @param $id int
     */
    public function actionLock(int $id)
    {
        $this->productParameterGroupEntity = $this->checkRequest((int)$id, ProductParameterGroupRepositoryFactory::class);
        $this->template->group = $this->productParameterGroupEntity;
    }



    /**
     * @param int $id
     */
    public function handleRemove(int $id)
    {
        try {
            $this->database->beginTransaction();
            $facade = $this->productParameterGroupRemoveFacadeFactory->create();
            $facade->remove($id);
            $this->database->commit();

            $this->flashMessage(sprintf("Skupina parametrů byla smazána."), "success");
        } catch (ProductParameterGroupRemoveFacadeException $exception) {
            $this->database->rollBack();
            $this->flashMessage($exception->getMessage(), "danger");
        }
        $this->redirect("this");
    }



    /**
     * @return ProductParameterGroupForm
     */
    public function createComponentProductParameterGroupForm() : ProductParameterGroupForm
    {
        $form = $this->productParameterGroupFormFactory->create();
        $form->setProductParameterGroupEntity($this->productParameterGroupEntity);
        return $form;
    }



    /**
     * @return ProductParameterGroupList
     */
    public function createComponentProductParameterGroupList() : ProductParameterGroupList
    {
        $list = $this->productParameterGroupListFactory->create();
        return $list;
    }



    /**
     * @return ProductParameterGroupLockList
     */
    public function createComponentProductParameterGroupLockList() : ProductParameterGroupLockList
    {
        $list = $this->productParameterGroupLockListFactory->create();
        $list->setGroup($this->productParameterGroupEntity);
        return $list;
    }

}