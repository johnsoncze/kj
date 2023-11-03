<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Validity;

use App\Customer\Customer;
use App\Customer\CustomerNotFoundException;
use App\Customer\CustomerRepository;
use App\Helpers\Entities;
use App\Helpers\Prices;
use App\Product\Product;
use App\Product\Production\Time\TimeRepository;
use App\ShoppingCart\BirthdayDiscount;
use App\ShoppingCart\IShoppingCartPrice;
use App\ShoppingCart\Product\Discount;
use App\ShoppingCart\Product\ShoppingCartProduct;
use App\ShoppingCart\Product\ShoppingCartProductRepository;
use App\ShoppingCart\ShoppingCart;
use App\ShoppingCart\ShoppingCartDTO;
use App\ShoppingCart\ShoppingCartDTOFactory;
use App\ShoppingCart\ShoppingCartNotFoundException;
use Kdyby\Monolog\Logger;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ValidityFacade
{


	/** @var BirthdayDiscount */
	private $birthdayDiscount;

	/** @var ShoppingCartDTOFactory */
	private $cartDTOFactory;

	/** @var Discount */
	private $cartProductDiscount;

	/** @var ShoppingCartProductRepository */
	private $cartProductRepo;

	/** @var CustomerRepository */
	private $customerRepo;

	/** @var Logger */
	private $logger;

	/** @var TimeRepository */
	private $productionTimeRepo;

	/** @var ITranslator */
	private $translator;

	/** @var Message[]|array */
	private $messages = [];



	public function __construct(BirthdayDiscount $birthdayDiscount,
								CustomerRepository $customerRepository,
								Discount $cartProductDiscount,
								Logger $logger,
								ShoppingCartDTOFactory $shoppingCartDTOFactory,
								ShoppingCartProductRepository $shoppingCartProductRepository,
                                TimeRepository $timeRepo,
								ITranslator $translator)
	{
		$this->birthdayDiscount = $birthdayDiscount;
		$this->cartProductDiscount = $cartProductDiscount;
		$this->customerRepo = $customerRepository;
		$this->logger = $logger;
		$this->cartDTOFactory = $shoppingCartDTOFactory;
		$this->cartProductRepo = $shoppingCartProductRepository;
		$this->productionTimeRepo = $timeRepo;
		$this->translator = $translator;
	}



	/**
	 * @param $cartId int
	 * @return Message[]|array
	 * @throws ValidityFacadeException
	 */
	public function validity(int $cartId) : array
	{
		try {
			$cart = $this->cartDTOFactory->createById($cartId);
			$this->checkProducts($cart);
			$this->checkDelivery($cart);
			$this->checkPayment($cart);

			//customer
			$cartEntity = $cart->getEntity();
			$customer = $cartEntity->getCustomerId() ? $this->checkCustomer($cartEntity) : NULL;
			$customer && $cartEntity->isAppliedBirthdayCoupon() && $this->checkBirthdayCoupon($cart, $customer);

			//process messages from during validity
			$messages = $this->messages;
			$this->logMessages();
			$this->cleanMessages();

			return $messages;
		} catch (ShoppingCartNotFoundException $exception) {
			throw new ValidityFacadeException($exception->getMessage());
		}
	}



	/**
	 * Check products.
	 * @param $cart ShoppingCartDTO
	 * @return ShoppingCartDTO
	 */
	protected function checkProducts(ShoppingCartDTO $cart) : ShoppingCartDTO
	{
		/** @var $products ShoppingCartProduct[]|array */
		$catalogProductsDTO = [];
		$products = $cart->getProducts();
		$products = $products ? Entities::setValueAsKey($products, 'productId') : [];

		//is products still available?
		foreach ($products as $product) {
			$catalogProduct = $product->getCatalogProduct();
			if ($catalogProduct === NULL) {
				$message = $this->translator->translate('shopping-cart.product.unavailable', ['name' => $product->getTranslatedName()]);
				$this->messages[] = new Message($message);
				continue;
			}
			$catalogProductsDTO[] = $cart->getProductDTOByProductId($product->getProductId());
		}

		foreach ($catalogProductsDTO as $productDTO) {

			/**
			 * @var $catalogProduct Product
			 * @var $cartProduct ShoppingCartProduct
			 */
			$catalogProduct = $productDTO->getProduct();
			$cartProduct = $products[$catalogProduct->getId()];

			//production time is not available
			if ($cartProduct->getProductionTimeId() && !$cartProduct->getProductionTime()) {
				$message = $this->translator->translate('shopping-cart.product.productionTime.notAvailable', ['product' => $cartProduct->getTranslatedName()]);
				$this->messages[] = new Message($message);
				continue;
			}

			//product is not in stock and it's state is production and product does not have a selected production time
			if ($catalogProduct->isInStock() !== TRUE
				&& $productDTO->getState()->isProduction() === TRUE
				&& !$cartProduct->getProductionTimeId()
			) {
			    $productionTime = $this->productionTimeRepo->findDefaultPublished();
			    if ($productionTime) {
			        $cartProduct->setProductionTimeId($productionTime->getId());
			        $this->cartProductRepo->save($cartProduct);

                    $message = $this->translator->translate('shopping-cart.product.productionTime.setDefault', ['name' => $cartProduct->getTranslatedName()]);
                    $this->messages[] = new Message($message, Message::TYPE_INFO);
                    continue;
                }

				$message = $this->translator->translate('shopping-cart.product.productionTime.productUnavailableAlready', ['name' => $cartProduct->getTranslatedName()]);
				$this->messages[] = new Message($message, Message::TYPE_INFO);
				continue;
			}

			//product has production time but product is in stock already
			if ($cartProduct->getProductionTime() && $catalogProduct->isInStock() === TRUE) {
				$cartProduct->removeProductionTime();
				$message = $this->translator->translate('shopping-cart.product.productionTime.productAvailableAlready', ['name' => $cartProduct->getTranslatedName()]);
				$this->messages[] = new Message($message, Message::TYPE_INFO);

				$availableQuantity = $catalogProduct->getStock();
				$isCartQuantityAvailable = $cartProduct->getQuantity() <= $availableQuantity;
				if (!$isCartQuantityAvailable) {
					$cartProduct->setQuantity($availableQuantity);
					$message = $this->translator->translate('shopping-cart.product.doesNotHaveEnoughQuantity', ['name' => $cartProduct->getTranslatedName(), 'quantity' => $availableQuantity]);
					$this->messages[] = new Message($message, Message::TYPE_INFO);
				}

				$this->cartProductRepo->save($cartProduct);
				continue;
			}

			//is product in stock?
			if (!$cartProduct->getProductionTime() && $catalogProduct->isInStock() !== TRUE) {
				$message = $this->translator->translate('product.stock.zeroWithName', ['name' => $cartProduct->getTranslatedName()]);
				$this->messages[] = new Message($message);
				continue;
			}

			//is product in required quantity?
			if (!$cartProduct->getProductionTime() && $catalogProduct->hasEnoughQuantity($cartProduct->getQuantity()) !== TRUE) {
				$availableQuantity = $catalogProduct->getStock();
				$cartProduct->setQuantity($availableQuantity);
				$this->cartProductRepo->save($cartProduct);

				$message = $this->translator->translate('shopping-cart.product.doesNotHaveEnoughQuantity', ['name' => $cartProduct->getTranslatedName(), 'quantity' => $availableQuantity]);
				$this->messages[] = new Message($message, Message::TYPE_INFO);
			}

			//check product price
			$original = (float)$cartProduct->getPrice();
			$current = (float)$catalogProduct->getPrice();
			if ($original !== $current) {
				$cartProduct->setPrice($current);
				$cartProduct->setVat($catalogProduct->getVat());
				$this->cartProductRepo->save($cartProduct);

				$message = $this->translator->translate('shopping-cart.product.priceChanged', [
					'name' => $cartProduct->getName(),
					'o' => Prices::toUserFriendlyFormat($original),
					'c' => Prices::toUserFriendlyFormat($current),
					'currency' => $this->translator->translate('price.currency.label'),
				]);
				$this->messages[] = new Message($message, Message::TYPE_INFO);
			}

			//check discount
			if ($cartProduct->getDiscount() && $cartProduct->getCatalogProduct()->isDiscountAllowed() !== TRUE) {
				$cartProduct->setDiscount(0.0);
				$this->cartProductRepo->save($cartProduct);

				$message = $this->translator->translate('shopping-cart.product.removedDiscount', [
					'name' => $cartProduct->getName(),
				]);
				$this->messages[] = new Message($message, Message::TYPE_INFO);
			}

			//set discount if product does not have
			if ($cart->getEntity()->getDiscount() && $cartProduct->getCatalogProduct()->isDiscountAllowed() && !$cartProduct->getDiscount()) {
				$this->cartProductDiscount->applyDiscount($cart->getEntity(), $cartProduct);
				$this->cartProductRepo->save($cartProduct);

				$message = $this->translator->translate('shopping-cart.product.appliedDiscount', [
					'name' => $cartProduct->getName(),
					'discount' => $cart->getEntity()->getDiscount(),
				]);
				$this->messages[] = new Message($message, Message::TYPE_INFO);
			}
		}

		return $cart;
	}



	/**
	 * @param $cart ShoppingCart
	 * @return Customer
	 */
	protected function checkCustomer(ShoppingCart $cart) : Customer
	{
		try {
			return $this->customerRepo->getOneAllowedById($cart->getCustomerId());
		} catch (CustomerNotFoundException $exception) {
			$message = $this->translator->translate($this->translator->translate('customer.not.found'));
			$this->messages[] = new Message($message);
		}
	}



	/**
	 * @param $cart ShoppingCartDTO
	 * @return ShoppingCartDTO
	 */
	protected function checkDelivery(ShoppingCartDTO $cart) : ShoppingCartDTO
	{
		if (!$cart->getDelivery() || !$cart->getDelivery()->getCatalogDelivery()) {
			$message = $this->translator->translate('shopping-cart.delivery.notFound');
			$this->messages[] = new Message($message);
		}
		return $cart;
	}



	/**
	 * Check payment.
	 * @param $cart ShoppingCartDTO
	 * @return ShoppingCartDTO
	 */
	protected function checkPayment(ShoppingCartDTO $cart) : ShoppingCartDTO
	{
        if (!$cart->getPayment() || !$cart->getPayment()->getCatalogPayment() || ($cart->hasNonStockableProducibleProduct() && $cart->getPayment()->getId() == 5)) {
			$message = $this->translator->translate('shopping-cart.payment.notFound');
			$this->messages[] = new Message($message);
		}
		return $cart;
	}



	/**
	 * @param $cart ShoppingCartDTO
	 * @param $customer Customer
	 * @return ShoppingCartDTO
	 */
	protected function checkBirthdayCoupon(ShoppingCartDTO $cart, Customer $customer) : ShoppingCartDTO
	{
		if ($cart->getEntity()->isAppliedBirthdayCoupon() === TRUE && ($customer->hasBirthdayCoupon() !== TRUE || !$cart->hasProductWithDiscountAllowed())) {
			$this->birthdayDiscount->remove($cart->getEntity());
			$message = $this->translator->translate('shopping-cart.birthdaycoupon.canNotBeAppliedAlready');
			$this->messages[] = new Message($message, Message::TYPE_INFO);
		}
		return $cart;
	}



	/**
	 * Log message.
	 * @param $message Message
	 * @return Message
	 */
	protected function logMessage(Message $message) : Message
	{
		$this->logger->log($message->getType(), $message->getMessage());
		return $message;
	}



	/**
	 * @return void
	 */
	protected function logMessages()
	{
		foreach ($this->messages as $message) {
			$this->logMessage($message);
		}
	}



	/**
	 * Clean stored messages.
	 * @return void
	 */
	protected function cleanMessages()
	{
		$this->messages = [];
	}
}