<?php

namespace App\Components\SortForm;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SortForm extends Control
{


    /** @var array */
    protected $items = [];

    /** @var callable */
    protected $onSuccess;



    /**
     * @param array $items
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->items = $items;
        return $this;
    }



    /**
     * @param callable $callback
     * @return $this
     */
    public function setOnSuccess(callable $callback)
    {
        $this->onSuccess = $callback;
        return $this;
    }



    /**
     * @return Form
     * @throws SortFormException
     */
    public function createComponentForm()
    {
        if (!$this->onSuccess) {
            throw new SortFormException("You must set onSuccess event.");
        }
        $form = new Form();
        foreach ($this->items as $id => $name) {
            $form->addText($id);
        }
        $form->addSubmit("submit", "Uložit")
            ->setAttribute("class", "btn btn-success");
        $form->onSuccess[] = [$this, "formSuccess"];
        return $form;
    }



    /**
     * @param Form $form
     */
    public function formSuccess(Form $form)
    {
        //Serialize
        $data = [];
        $i = 1;
        foreach ($form->getHttpData() as $name => $val) {
            if ($component = $form->getComponent($val, FALSE)) {
                $data[$i] = $val;
                $i++;
            }
        }

        //Call user defined callback
        call_user_func_array($this->onSuccess, [$form, $data]);
    }



    /**
     * @return void
     */
    public function render()
    {
        if ($this->items) {
            $this->template->setFile(__DIR__ . "/templates/sorting.latte");
            $this->template->items = $this->items;
        } else {
            $this->template->setFile(__DIR__ . "/templates/noItems.latte");
        }
        $this->template->render();
    }
}