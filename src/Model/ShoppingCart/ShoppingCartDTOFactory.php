<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

use App\Product\ProductDTO;
use App\Product\ProductDTOFactory;
use App\ShoppingCart\Delivery\ShoppingCartDelivery;
use App\ShoppingCart\Delivery\ShoppingCartDeliveryNotFoundException;
use App\ShoppingCart\Delivery\ShoppingCartDeliveryRepository;
use App\ShoppingCart\Payment\ShoppingCartPayment;
use App\ShoppingCart\Payment\ShoppingCartPaymentNotFoundException;
use App\ShoppingCart\Payment\ShoppingCartPaymentRepository;
use App\ShoppingCart\Price\Calculator;
use App\ShoppingCart\Price\Price;
use App\ShoppingCart\Product\Price\PriceCalculator;
use App\ShoppingCart\Product\ShoppingCartProduct;
use App\ShoppingCart\Product\ShoppingCartProductRepository;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartDTOFactory
{


	/** @var Calculator */
	protected $priceCalculator;

	/** @var ProductDTOFactory */
	protected $productDTOFactory;

	/** @var PriceCalculator */
	protected $productPriceCalculator;

	/** @var ShoppingCartDeliveryRepository */
	protected $shoppingCartDeliveryRepo;

	/** @var ShoppingCartPaymentRepository */
	protected $shoppingCartPaymentRepo;

	/** @var ShoppingCartProductRepository */
	protected $shoppingCartProductRepo;

	/** @var ShoppingCartRepository */
	protected $shoppingCartRepo;

	/** @var ITranslator */
	protected $translator;



	/**
	 * ShoppingCartDTOFactory constructor.
	 * @param Calculator $priceCalculator
	 * @param ProductDTOFactory $productDTOFactory
	 * @param PriceCalculator $productPriceCalculator
	 * @param ShoppingCartDeliveryRepository $shoppingCartDeliveryRepo
	 * @param ShoppingCartPaymentRepository $shoppingCartPaymentRepo
	 * @param ShoppingCartProductRepository $shoppingCartProductRepo
	 * @param ShoppingCartRepository $shoppingCartRepo
	 * @param ITranslator $translator
	 */
	public function __construct(Calculator $priceCalculator,
								ProductDTOFactory $productDTOFactory,
								PriceCalculator $productPriceCalculator,
								ShoppingCartDeliveryRepository $shoppingCartDeliveryRepo,
								ShoppingCartPaymentRepository $shoppingCartPaymentRepo,
								ShoppingCartProductRepository $shoppingCartProductRepo,
								ShoppingCartRepository $shoppingCartRepo,
								ITranslator $translator)
	{
		$this->priceCalculator = $priceCalculator;
		$this->productDTOFactory = $productDTOFactory;
		$this->productPriceCalculator = $productPriceCalculator;
		$this->shoppingCartDeliveryRepo = $shoppingCartDeliveryRepo;
		$this->shoppingCartPaymentRepo = $shoppingCartPaymentRepo;
		$this->shoppingCartProductRepo = $shoppingCartProductRepo;
		$this->shoppingCartRepo = $shoppingCartRepo;
		$this->translator = $translator;
	}



	/**
	 * todo test
	 * @param $id int
	 * @return ShoppingCartDTO
	 * @throws ShoppingCartNotFoundException
	 */
	public function createById(int $id) : ShoppingCartDTO
	{
		$cart = $this->shoppingCartRepo->getOneById($id, $this->translator);
		$cartDelivery = $this->getDelivery($cart);
		$cartPayment = $this->getPayment($cart);
		$this->shoppingCartProductRepo->removeInvalidProducts($cart->getId());
		$cartProducts = $this->shoppingCartProductRepo->findByCartId($cart->getId());
		$catalogProductsDTO = $cartProducts ? $this->createCatalogProductsDTO($cartProducts) : [];

		return $this->createDTO($cart, $cartDelivery, $cartPayment, $cartProducts, $catalogProductsDTO);
	}



	/**
	 * @param $cartProducts ShoppingCartProduct[]
	 * @return Price
	 */
	protected function calculateSummaryPrice(array $cartProducts)
	{
		return $this->priceCalculator->calculate(array_map(function (ShoppingCartProduct $cartProduct) {
			return $this->productPriceCalculator->calculate($cartProduct);
		}, $cartProducts));
	}



	/**
	 * @param $cart ShoppingCart
	 * @param $cartDelivery ShoppingCartDelivery|null
	 * @param $cartPayment ShoppingCartPayment|null
	 * @param $cartProducts ShoppingCartProduct[]|array
	 * @param $catalogProductsDTO ProductDTO[]|array
	 * @return ShoppingCartDTO
	 */
	protected function createDTO(ShoppingCart $cart,
								 ShoppingCartDelivery $cartDelivery = NULL,
								 ShoppingCartPayment $cartPayment = NULL,
								 array $cartProducts = [],
								 array $catalogProductsDTO = []) : ShoppingCartDTO
	{
		$cartDTO = new ShoppingCartDTO($cart);
		$cartDelivery && $cartDTO->setDelivery($cartDelivery);
		$cartPayment && $cartDTO->setPayment($cartPayment);

		foreach ($cartProducts as $cartProduct) {
			if ($catalogProduct = $cartProduct->getCatalogProduct()) {
				$catalogProductDTO = $catalogProductsDTO[$catalogProduct->getId()];
			}
			$cartDTO->addProduct($cartProduct, $catalogProductDTO ?? NULL);
		}

		$summaryPrice = $cartProducts ? $this->calculateSummaryPrice($cartProducts) : new Price();
		$cartDTO->setPrice($summaryPrice);

		return $cartDTO;
	}



	/**
	 * @param $cartProducts ShoppingCartProduct[]
	 * @return array
	 */
	protected function createCatalogProductsDTO(array $cartProducts) : array
	{
		$catalogProducts = [];
		foreach ($cartProducts as $cartProduct) {
			if ($catalogProduct = $cartProduct->getCatalogProduct()) {
				$catalogProducts[] = $catalogProduct;
			}
		}
		return $catalogProducts ? $this->productDTOFactory->createFromProducts($catalogProducts, TRUE, TRUE) : [];
	}



	/**
	 * @param $cart ShoppingCart
	 * @return ShoppingCartDelivery|null
	 */
	protected function getDelivery(ShoppingCart $cart)
	{
		try {
			return $this->shoppingCartDeliveryRepo->getOneByShoppingCartId($cart->getId());
		} catch (ShoppingCartDeliveryNotFoundException $exception) {
			return NULL;
		}
	}



	/**
	 * @param $cart ShoppingCart
	 * @return ShoppingCartPayment|null
	 */
	protected function getPayment(ShoppingCart $cart)
	{
		try {
			return $this->shoppingCartPaymentRepo->getOneByShoppingCartId($cart->getId());
		} catch (ShoppingCartPaymentNotFoundException $exception) {
			return NULL;
		}
	}
}