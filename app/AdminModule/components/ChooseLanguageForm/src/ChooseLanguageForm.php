<?php

namespace App\Components\ChooseLanguageForm;

use App\Language\LanguageListFacadeFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ChooseLanguageForm extends Control
{


    /** @var LanguageListFacadeFactory */
    protected $languageListFacadeFactory;

    /** @var callable[]|array */
    protected $onSuccess = [];

    /** @var callable|null */
    protected $createFormCallback;



    public function __construct(LanguageListFacadeFactory $languageListFacadeFactory)
    {
        parent::__construct();
        $this->languageListFacadeFactory = $languageListFacadeFactory;
    }



    /**
     * @param callable $callback
     * @return $this
     */
    public function createFormCallback(callable $callback)
    {
        $this->createFormCallback = $callback;
        return $this;
    }



    /**
     * @param $onSuccess callable
     * @return self
     */
    public function addOnSuccess(callable $onSuccess)
    {
        $this->onSuccess[] = $onSuccess;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();
        $form->addSelect("language", "Jazyk*", $this->languageListFacadeFactory->create()->getList())
            ->setRequired("Vyberte jazyk.")
            ->setAttribute("class", "form-control")
            ->setPrompt("- Vyberte -");
        if (is_callable($this->createFormCallback)) {
            call_user_func($this->createFormCallback, $form);
        }
        $form->addSubmit("submit", "Vybrat")
            ->setAttribute("class", "btn btn-success");
        $form->onSuccess = $this->onSuccess;
        return $form;
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }

}