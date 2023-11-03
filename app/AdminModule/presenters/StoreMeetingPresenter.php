<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\OpportunityList\OpportunityList;
use App\Opportunity\Opportunity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class StoreMeetingPresenter extends DemandPresenter
{


    public function renderDetail()
    {
        parent::renderDetail();
        $this->template->setFile(__DIR__ . '/templates/Demand/detail.latte');
    }



    /**
     * @return OpportunityList
     */
    public function createComponentOpportunityList() : OpportunityList
    {
        $list = $this->opportunityListFactory->create();
        $list->addType(Opportunity::TYPE_ORDER_FINISH_ON_STORE);
		$list->addType(Opportunity::TYPE_PRODUCT_STORE_MEETING);
        $list->addType(Opportunity::TYPE_STORE_MEETING);
        return $list;
    }
}