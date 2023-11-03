<?php

declare(strict_types = 1);

namespace App\ShoppingCart;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ShoppingCartFacade
{


	/** @var ShoppingCartDTOFactory */
	private $cartDTOFactory;



	public function __construct(ShoppingCartDTOFactory $cartDTOFactory)
	{
		$this->cartDTOFactory = $cartDTOFactory;
	}



	/**
	 * Get shopping cart data transfer object.
	 * @param $cartId int
	 * @return ShoppingCartDTO
	 * @throws ShoppingCartFacadeException
	 * todo test
	 */
	public function getDTO(int $cartId) : ShoppingCartDTO
	{
		try {
			return $this->cartDTOFactory->createById($cartId);
		} catch (ShoppingCartNotFoundException $exception) {
			throw new ShoppingCartFacadeException($exception->getMessage());
		}
	}
}