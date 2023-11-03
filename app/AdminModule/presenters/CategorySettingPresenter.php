<?php

namespace App\AdminModule\Presenters;

use App\Category\CategoryEntity;
use App\Category\CategoryRepositoryFactory;
use App\Helpers\Presenters;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class CategorySettingPresenter extends AdminModulePresenter
{


    /** @persistent */
    public $categoryId;

    /** @var CategoryEntity|null */
    protected $categoryEntity;



    public function startup()
    {
        parent::startup();

        //check category
        $this->categoryEntity = $this->checkRequest((int)$this->categoryId, CategoryRepositoryFactory::class);
        $this->template->categoryEntity = $this->categoryEntity;
    }



    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->title .= " <small>{$this->categoryEntity->getName()}</small>";
    }


}