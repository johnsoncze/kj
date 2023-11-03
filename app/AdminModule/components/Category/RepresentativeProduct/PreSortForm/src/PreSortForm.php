<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\RepresentativeProduct\PreSortForm;

use App\Category\Product\Related\Product;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class PreSortForm extends Control
{


	/** @var string */
	const PARAMETER_TYPE = 'type';



	/**
	 * @return Form
	 */
	public function createComponentForm() : Form
	{
		$form = new Form();
		$form->addSelect('type', 'Typ*', Product::getTypeList())
			->setAttribute('class', 'form-control')
			->setPrompt('- Vyberte -')
			->setRequired('Vyberte umístění.');
		$form->addSubmit('submit', 'Vybrat')
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
		$parameters[self::PARAMETER_TYPE] = $values->type;
		$presenter->redirect('this', $parameters);
	}



	public function render()
	{
		$this->template->setFile(__DIR__ . '/default.latte');
		$this->template->render();
	}
}