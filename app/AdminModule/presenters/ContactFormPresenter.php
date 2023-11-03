<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\OpportunityList\OpportunityList;
use App\Opportunity\Opportunity;
use App\Opportunity\OpportunityRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ContactFormPresenter extends DemandPresenter
{


    /**
     * Action 'detail'.
     * @param $id int
     * @return void
     */
    public function actionDetail(int $id)
    {
        $this->opportunity = $opportunity = $this->checkRequest($id, OpportunityRepository::class);

        $this->template->opportunity = $opportunity;
    }



    public function renderDetail()
    {
        parent::renderDetail();
        $this->template->setFile(__DIR__ . '/templates/Demand/detail.latte');
    }



    /**
     * @return OpportunityList
     */
    public function createComponentContactFormMessageList() : OpportunityList
    {
        $list = $this->opportunityListFactory->create();
        $list->addType(Opportunity::TYPE_CONTACT_FORM);
        return $list;
    }
}