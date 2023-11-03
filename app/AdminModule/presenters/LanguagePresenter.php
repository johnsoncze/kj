<?php

namespace App\AdminModule\Presenters;

use App\Components\LanguageList\LanguageList;
use App\Components\LanguageList\LanguageListFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @breadcrumb-nav-parent :Admin:Setting:default
 */
class LanguagePresenter extends AdminModulePresenter
{


    /** @var LanguageListFactory @inject */
    public $languageListFactory;



    /**
     * @return LanguageList
     */
    public function createComponentLanguageList()
    {
        return $this->languageListFactory->create();
    }
}