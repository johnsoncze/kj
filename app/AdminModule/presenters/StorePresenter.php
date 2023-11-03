<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Store\OpeningHours\ChangeForm\ChangeForm;
use App\AdminModule\Components\Store\OpeningHours\ChangeForm\ChangeFormFactory;
use App\AdminModule\Components\Store\OpeningHours\ChangeList\ChangeList;
use App\AdminModule\Components\Store\OpeningHours\ChangeList\ChangeListFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @breadcrumb-nav-parent :Admin:Setting:default
 */
final class StorePresenter extends AdminModulePresenter
{


    /** @var ChangeFormFactory @inject */
    public $openingHoursChangeFormFactory;

    /** @var ChangeListFactory @inject */
    public $openingHoursChangeListFactory;



    /**
     * @return ChangeForm
     */
    public function createComponentOpeningHoursChangeForm() : ChangeForm
    {
        return $this->openingHoursChangeFormFactory->create();
    }



    /**
     * @return ChangeList
     */
    public function createComponentOpeningHoursChangeList() : ChangeList
    {
        return $this->openingHoursChangeListFactory->create();
    }
}