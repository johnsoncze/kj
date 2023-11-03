<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\ShoppingCart\ButtonNavigation;

use App\ShoppingCart\ShoppingCartDTO;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ButtonNavigation extends Control
{


	/** @var ShoppingCartDTO|null */
	private $shoppingCart;



	/**
	 * @param $shoppingCart ShoppingCartDTO
	 * @return self
	 */
	public function setCart(ShoppingCartDTO $shoppingCart) : self
	{
		$this->shoppingCart = $shoppingCart;
		return $this;
	}



	public function render()
	{
		$this->template->data = $this->getData($this->getPresenter()->getAction());
		$this->template->shoppingCart = $this->shoppingCart;
		$this->template->setFile(__DIR__ . '/default.latte');
		$this->template->render();
	}



	/**
	 * @param $action string
	 * @return array
	 */
	private function getData(string $action) : array
	{
		$data = [

			'step1' => [
				'back' => [
					'anchor' => 'shopping-cart.step.step1.back.anchor',
					'route' => 'Homepage:default',
				], 'next' => [
					'anchor' => 'shopping-cart.step.step1.next.anchor',
					'route' => 'step2',
				],
			],

			'step1links' => [
				'back' => [
					'anchor' => 'shopping-cart.step.step1links.back.anchor',
					'route' => 'step1',
				], 'next' => [
					'anchor' => 'shopping-cart.step.step1links.next.anchor',
					'route' => 'step3',
				],
			],

			'step2' => [
				'back' => [
					'anchor' => 'shopping-cart.step.step2.back.anchor',
                    'anchor2' => 'shopping-cart.step.step2.back.anchor2',
                    'route' => 'step1',
				], 'next' => [
					'anchor' => 'shopping-cart.step.step2.next.anchor',
					'route' => NULL,
				],
			],

			'step3' => [
				'back' => [
					'anchor' => 'shopping-cart.step.step3.back.anchor',
                    'anchor2' => 'shopping-cart.step.step3.back.anchor2',
					'route' => 'step2',
				], 'next' => [
					'anchor' => 'shopping-cart.step.step3.next.anchor',
					'route' => NULL,
				],
			],

			'step3Recapitulation' => [
				'back' => [
					'anchor' => 'shopping-cart.step.step3Recapitulation.back.anchor',
					'route' => 'step3',
				], 'next' => [
					'anchor' => 'shopping-cart.step.step3Recapitulation.next.anchor',
					'route' => 'step4',
				],
			],
		];

		return $data[$action] ?? [];
	}
}