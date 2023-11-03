<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Store\OpeningHours\ChangeForm;

use App\Store\OpeningHours\Change\ChangeFacadeException;
use App\Store\OpeningHours\Change\ChangeFacadeFactory;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ChangeForm extends Control
{


    /** @var Context */
    private $database;

    /** @var ChangeFacadeFactory */
    private $changeFacadeFactory;



    public function __construct(Context $context,
                                ChangeFacadeFactory $changeFacadeFactory)
    {
        parent::__construct();
        $this->database = $context;
        $this->changeFacadeFactory = $changeFacadeFactory;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $timeList = $this->getTimeList();

        $form = new Form();
        $form->addText('date', 'Datum')
            ->setAttribute('class', 'form-control datepicker')
            ->setAttribute('autocomplete', 'off')
            ->setRequired('Vyplňte datum.');
        $closed = $form->addCheckbox('closed', ' Celý den zavřeno');
        $form->addSelect('openingTime', 'Čas otevření', $timeList)
            ->setPrompt('- vyberte -')
            ->setAttribute('class', 'form-control')
            ->addConditionOn($closed, Form::BLANK)
            ->setRequired('Vyberte čas.');
        $form->addSelect('closingTime', 'Čas zavření', $timeList)
            ->setPrompt('- vyberte -')
            ->setAttribute('class', 'form-control')
            ->addConditionOn($closed, Form::BLANK)
            ->setRequired('Vyberte čas.');
        $form->addSubmit('submit', 'Přidat')
            ->setAttribute('class', 'btn btn-success');
        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }



    /**
     * @param $form Form
     * @return void
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $presenter = $this->getPresenter();

        $openingTime = $values->closed ? NULL : ($values->openingTime ?: NULL);
        $closingTime = $values->closed ? NULL : ($values->closingTime ?: NULL);

        try {
            $this->database->beginTransaction();
            $changeFacade = $this->changeFacadeFactory->create();
            $changeFacade->add($values->date, $openingTime, $closingTime);
            $this->database->commit();

            $presenter->flashMessage('Vyjímka byla přidána.', 'success');
            $presenter->redirect('this');
        } catch (ChangeFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * Get time list.
     * @return array
     */
    private function getTimeList() : array
    {
        $list = [];
        for ($h = 0; $h <= 23; $h++) {
            for ($m = 0; $m <= 45; $m += 15) {
                $time = sprintf('%02s', $h) . ':' . sprintf('%02s', $m);
                $list[$time] = $time;
            }
        }
        return $list;
    }
}