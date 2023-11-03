<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Store\OpeningHours\ChangeList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Store\OpeningHours\Change\ChangeFacadeFactory;
use App\Store\OpeningHours\Change\ChangeRepository;
use Grido\Grid;
use Nette\Application\AbortException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ChangeList extends GridoComponent
{


    /** @var ChangeFacadeFactory */
    private $changeFacadeFactory;

    /** @var ChangeRepository */
    private $changeRepo;



    public function __construct(ChangeFacadeFactory $changeFacadeFactory,
                                ChangeRepository $changeRepository,
                                GridoFactory $gridoFactory)
    {
        parent::__construct($gridoFactory);
        $this->changeFacadeFactory = $changeFacadeFactory;
        $this->changeRepo = $changeRepository;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $timeReplacement = [null => 'Zavřeno'];

        $source = new RepositorySource($this->changeRepo);
        $source->setDefaultSort('date', 'DESC');

        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $grid->addColumnDate('date', 'Datum')
            ->setDateFormat('d.m.Y')
            ->setSortable();
        $grid->addColumnDate('openingTime', 'Čas otevření')
            ->setDateFormat('H:i')
            ->setReplacement($timeReplacement);
        $grid->addColumnDate('closingTime', 'Čas zavření')
            ->setDateFormat('H:i')
            ->setReplacement($timeReplacement);
        $grid->addActionHref('delete', '', $this->getName()  . '-delete!')
            ->setIcon('trash')
            ->setCustomRender(function ($row) {
                $link = $this->link('delete!', ['id' => $row->getId()]);
                $confirm = sprintf('Opravdu si přejete smazat vyjímku pro datum \'%s\'?', (new \DateTime($row->getDate()))->format('d.m.Y'));
                return sprintf('<a href="%s" 
                                   class="grid-action-removeVariant btn btn-default btn-xs btn-mini" 
                                   data-grido-confirm="%s"><i class="fa fa-trash"></i></a>', $link, $confirm);
            });

        //styles
        $grid->getColumn('date')->getHeaderPrototype()->style['width'] = '30%';
        $grid->getColumn('openingTime')->getHeaderPrototype()->style['width'] = '30%';
        $grid->getColumn('closingTime')->getHeaderPrototype()->style['width'] = '30%';

        return $grid;
    }



    /**
     * Delete change.
     * @param $id int
     * @return void
     * @throws AbortException
     */
    public function handleDelete(int $id)
    {
        $presenter = $this->getPresenter();
        $changeFacade = $this->changeFacadeFactory->create();
        $changeFacade->remove($id);
        $presenter->flashMessage('Vyjímka byla smazána.', 'success');
        $presenter->redirect('this');
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}