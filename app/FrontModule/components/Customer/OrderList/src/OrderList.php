<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Customer\OrderList;

use App\Customer\Customer;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Prices;
use App\Order\Order;
use App\Order\OrderRepository;
use Grido\Grid;
use Kdyby\Translation\ITranslator;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\Control;
use Nette\Utils\Html;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OrderList extends Control
{
    protected $linkGenerator;


    /** @var Customer|null */
    private $customer;

    /** @var GridoFactory */
    private $gridFactory;

    /** @var OrderRepository */
    private $orderRepo;

    /** @var ITranslator */
    private $translator;


    public function __construct
    (
        GridoFactory $gridoFactory,
        ITranslator $translator,
        OrderRepository $orderRepository,
        LinkGenerator $linkGenerator
    )
    {
        $this->linkGenerator = $linkGenerator;
        $this->gridFactory = $gridoFactory;
        $this->orderRepo = $orderRepository;
        $this->translator = $translator;
        parent::__construct();
    }



    /**
     * @param Customer $customer
     * @return self
     */
    public function setCustomer(Customer $customer) : self
    {
        $this->customer = $customer;
        return $this;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        //source
        $source = new RepositorySource($this->orderRepo);
        $source->setDefaultSort('addDate', 'DESC');
        $source->filter([['customerId', '=', $this->customer->getId()]]);

        $grid = $this->gridFactory->createFrontend();
        $grid->setModel($source);
		$grid->setDefaultPerPage(5);

        //columns
        $code = $grid->addColumnText('code', $this->translator->translate('order.list.column.code.label'));
        $code->setSortable()->setFilterText();
        $code->getHeaderPrototype()->style['width'] = '20%';

        $createDate = $grid->addColumnDate('addDate', $this->translator->translate('order.list.column.createDate.label'));
        $createDate->setDateFormat('d.m.Y H:i:s');
        $createDate->setSortable();
        $createDate->getHeaderPrototype()->style['width'] = '20%';

        $price = $grid->addColumnNumber('summaryPrice', $this->translator->translate('price.label.default'));
        $price->setCustomRender(function(Order $order) {
        	return Prices::toUserFriendlyFormat($order->getSummaryPrice()) . ' ' . $this->translator->translate('price.currency.label');
		});
        $price->setSortable();
        $price->getHeaderPrototype()->style['min-width'] = '150px';

        $repayment = $grid->addColumnLink('repayment', '');

        $linkGenerator = $this->linkGenerator;
        $repayment->setCustomRender(function (Order $order) use ($linkGenerator) {
            if ($order->isGatewayPaymentAvailable()) {
                $link = $linkGenerator->link('Front:PaymentGateway:createRequest', ['token' => $order->getToken()]);
                return '<a href="' . $link . '" class="Button">Opakovat platbu</a>';
            } else {
                return '';
            }
        });

        $stateList = Order::getTranslatedStateList($this->translator);
        $state = $grid->addColumnText('state', $this->translator->translate('general.state.label'));
        $state->setSortable();
        $state->setReplacement($stateList);
        $state->getHeaderPrototype()->style['width'] = '20%';

        //actions
        $grid->setPrimaryKey('code');
        $grid->addActionHref('detail', '', 'Account:orderDetail')
            ->setCustomRender(function (Order $order) {
                $link = $this->getPresenter()->link('Account:orderDetail', ['code' => $order->getCode()]);
                return Html::el('a')
                    ->setHtml($this->translator->translate('order.list.cta.detail.label'))
                    ->setAttribute('href', $link);
            });

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/templates/default.latte');
        $this->template->render();
    }



    public function renderLastOrder()
    {
        $this->template->order = $this->orderRepo->findOneLastByCustomerId($this->customer->getId());
        $this->template->setFile(__DIR__ . '/templates/lastOrder.latte');
        $this->template->render();
    }
}