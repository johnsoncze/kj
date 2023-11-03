<?php

declare(strict_types=1);

namespace App\AdminModule\Components\Newsletter\SubscriberList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Newsletter\Subscriber\Subscriber;
use App\Newsletter\Subscriber\SubscriberFacade;
use App\Newsletter\Subscriber\SubscriberFacadeFactory;
use App\Newsletter\Subscriber\SubscriberRepository;
use Grido\Grid;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SubscriberList extends GridoComponent
{


    /** @var SubscriberRepository */
    private $subscriberRepo;

    /** @var SubscriberFacadeFactory */
    private $subscriberFacadeFactory;


    public function __construct(GridoFactory $gridoFactory,
                                SubscriberRepository $subscriberRepository,
                                SubscriberFacadeFactory $subscriberFacadeFactory
    )
    {
        parent::__construct($gridoFactory);
        $this->subscriberRepo = $subscriberRepository;
        $this->subscriberFacadeFactory = $subscriberFacadeFactory;
    }


    /**
     * @return Grid
     */
    public function createComponentList(): Grid
    {
        $source = new RepositorySource($this->subscriberRepo);
        $source->setDefaultSort('email', 'ASC');

        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $confirmList = [1 => 'Ano', 0 => 'Ne'];
        $grid->addColumnText('email', 'E-mail')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('confirmed', 'Potvrzen')
            ->setReplacement($confirmList)
            ->setSortable()
            ->setFilterSelect(Arrays::mergeTree(['' => ''], $confirmList));
        $grid->addColumnDate('addDate', 'Datum přidání')
            ->setDateFormat('d.m.Y H:i:s')
            ->setSortable()
            ->setFilterDateRange();

        $grid->addActionEvent('confirm', 'Potvrdit', function ($id) {
            /** @var  Subscriber */
            $subscriber = $this->subscriberRepo->findOneBy([
                "where" => [
                    ["id", "=", intval($id)]
                ]
            ]);
            if ($subscriber->confirmToken) {
                /** @var SubscriberFacade */
                $subscriberFacade = $this->subscriberFacadeFactory->create();
                $subscriberFacade->confirm($subscriber->email, $subscriber->confirmToken);
            }
        })->setDisable(function ($subscriber) {
            return $subscriber->confirmed;
        });

        //styles
        $grid->getColumn('email')->getHeaderPrototype()->style['width'] = '40%';
        $grid->getColumn('confirmed')->getHeaderPrototype()->style['width'] = '20%';
        $grid->getColumn('addDate')->getHeaderPrototype()->style['width'] = '20%';

        return $grid;
    }


    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}