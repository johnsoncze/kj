<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\StateChangeForm;

use Kdyby\Translation\ITranslator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class StateChangeForm extends Control
{


    /** @var ITranslator */
    private $translator;

    /** @var IStateObject|null */
    private $stateObject;

    /** @var callable|null */
    private $successCallback;



    public function __construct(ITranslator $translator)
    {
        parent::__construct();
        $this->translator = $translator;

    }



    /**
     * @param $object IStateObject
     * @return self
     */
    public function setStateObject(IStateObject $object) : self
    {
        $this->stateObject = $object;
        return $this;
    }



    /**
     * @param callable $callback
     * @return self
     */
    public function setSuccessCallback(callable $callback) : self
    {
        $this->successCallback = $callback;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $stateObject = $this->stateObject;
        $stateList = $stateObject::getTranslatedStateList($this->translator);

        $form = new Form();
        $form->addSelect('state', 'Stav: ', $stateList)
            ->setAttribute('class', 'form-control')
            ->setDefaultValue($stateObject->getState());
        $form->addSubmit('submit', 'Uložit')
            ->setAttribute('class', 'btn btn-success');
        $form->onSuccess[] = $this->successCallback;
        return $form;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}