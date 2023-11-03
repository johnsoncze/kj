<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\OpportunityList\OpportunityList;
use App\AdminModule\Components\OpportunityList\OpportunityListFactory;
use App\AdminModule\Components\OpportunityProductList\OpportunityProductList;
use App\AdminModule\Components\OpportunityProductList\OpportunityProductListFactory;
use App\AdminModule\Components\StateChangeForm\StateChangeForm;
use App\AdminModule\Components\StateChangeForm\StateChangeFormFactory;
use App\Opportunity\Opportunity;
use App\Opportunity\OpportunityRepository;
use App\Opportunity\OpportunityStorageFacadeException;
use App\Opportunity\OpportunityStorageFacadeFactory;
use App\Opportunity\Product\ProductRepository;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class DemandPresenter extends AdminModulePresenter
{


    /** @var Opportunity|null */
    public $opportunity;

    /** @var OpportunityListFactory @inject */
    public $opportunityListFactory;

    /** @var OpportunityRepository @inject */
    public $opportunityRepo;

    /** @var OpportunityProductListFactory @inject */
    public $opportunityProductListFactory;

    /** @var ProductRepository @inject */
    public $opportunityProductRepo;

    /** @var OpportunityStorageFacadeFactory @inject */
    public $opportunityStorageFacadeFactory;

    /** @var StateChangeFormFactory @inject */
    public $stateChangeFormFactory;



    /**
     * Action 'detail'.
     * @param $id int
     * @return void
     */
    public function actionDetail(int $id)
    {
        $this->opportunity = $opportunity = $this->checkRequest($id, OpportunityRepository::class);

        $this->template->opportunity = $opportunity;
        $this->template->products = $this->opportunityProductRepo->findByOpportunityId((int)$opportunity->getId());
    }



    /**
     * Render 'detail'.
     */
    public function renderDetail()
    {
        $this->addToHeadline($this->opportunity->getCode());
    }



    /**
     * @return OpportunityList
     */
    public function createComponentOpportunityList() : OpportunityList
    {
        $list = $this->opportunityListFactory->create();
        $list->addType(Opportunity::TYPE_PRODUCT_DEMAND);
        $list->addType(Opportunity::TYPE_WEEDING_RING_DEMAND);
        return $list;
    }



    /**
     * @return OpportunityProductList
     */
    public function createComponentOpportunityProductList() : OpportunityProductList
    {
        $list = $this->opportunityProductListFactory->create();
        $list->setOpportunity($this->opportunity);
        return $list;
    }



    /**
     * @return StateChangeForm
     */
    public function createComponentStateForm() : StateChangeForm
    {
        $form = $this->stateChangeFormFactory->create();
        $form->setStateObject($this->opportunity);
        $form->setSuccessCallback(function (Form $form) {
            try {
                $values = $form->getValues();

                $this->database->beginTransaction();
                $storageFacade = $this->opportunityStorageFacadeFactory->create();
                $storageFacade->changeState((int)$this->opportunity->getId(), $values->state);
                $this->database->commit();

                $this->flashMessage('Stav byl uložen.', 'success');
                $this->redirect('this');
            } catch (OpportunityStorageFacadeException $exception) {
                $this->database->rollBack();
                $this->flashMessage($exception->getMessage(), 'danger');
            }
        });
        return $form;
    }
}