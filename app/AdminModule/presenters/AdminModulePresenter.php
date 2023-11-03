<?php

namespace App\AdminModule\Presenters;


use App\Components\BreadcrumbNavigation\Navigation;
use App\Components\BreadcrumbNavigation\NavigationFactory;
use App\Components\BreadcrumbNavigation\PresenterExtension;
use App\FacadeException;
use App\Opportunity\Opportunity;
use App\Opportunity\OpportunityRepository;
use App\Order\Order;
use App\Order\OrderRepository;
use Nette\Application\LinkGenerator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class AdminModulePresenter extends BasePresenter
{


    /** @var string */
    const USER_IDENTITY_NAMESPACE = 'admin';

    /** @var string */
    const BACKLINK = "backlink";

    /** @var NavigationFactory @inject */
    public $navigationFactory;

    /** @var Navigation */
    protected $_navigation;

    /** @var OpportunityRepository @inject */
    public $opportunityRepo;

    /** @var OrderRepository @inject */
    public $orderRepo;



    public function startup()
    {
        parent::startup();
        try {
            $identity = $this->userFacadeFactory
                ->create()
                ->getUserLoggedIdentity($this->getUser());
            if ($this->getUser()->getIdentity()->getEntity()->isSupplier() === TRUE) {
                $this->redirect(':Front:Homepage:default');
            }
        } catch (FacadeException $exception) {
            $this->redirect(":Admin:Sign:in", [
                self::BACKLINK => $this->storeRequest("+1 hours")
            ]);
        }
        $this->setLayout(__DIR__ . "/templates/@layoutAdministration.latte");

        //breadcrumb navigation
        $extension = new PresenterExtension($this, $this->nameResolverFactory->create());
        $this->_navigation = $this->navigationFactory->create();
        $this->_navigation->extension($extension);
        $this->_navigation->setHomeLink($this->link("Homepage:default"));
    }



    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->ckEditorUploadPath = $this->link('CkEditor:upload');
        $this->template->linkGenerator = $this->context->getByType(LinkGenerator::class);
        $this->template->newContactFormMessageCount = $this->opportunityRepo->getCountByTypesAndState([Opportunity::TYPE_CONTACT_FORM], Opportunity::STATE_NEW);
        $this->template->newDemandCount = $this->opportunityRepo->getCountByTypesAndState([Opportunity::TYPE_PRODUCT_DEMAND, Opportunity::TYPE_WEEDING_RING_DEMAND], Opportunity::STATE_NEW);
        $this->template->newOrderCount = $this->orderRepo->getCountByState(Order::NEW_STATE);
        $this->template->newStoreMeetingCount = $this->opportunityRepo->getCountByTypesAndState([Opportunity::TYPE_STORE_MEETING, Opportunity::TYPE_PRODUCT_STORE_MEETING, Opportunity::TYPE_ORDER_FINISH_ON_STORE], Opportunity::STATE_NEW);
    }



    /**
     * @return Navigation
     */
    public function createComponentBreadcrumbNavigation()
    {
        return $this->_navigation;
    }



    /**
     * @param string $anchor
     * @param string $destination
     * @param array|NULL $args
     * @return AdminModulePresenter
     */
    public function setBackLink(string $anchor, string $destination, array $args = []) : self
    {
        $this->template->backLink = [
            "href" => $this->link($destination, $args),
            "anchor" => $anchor
        ];
        return $this;
    }



    /**
     * @param $text string
     * @return void
     */
    public function addToHeadline(string $text)
    {
        $this->template->title .= sprintf(' <small>%s</small>', $text);
    }
}