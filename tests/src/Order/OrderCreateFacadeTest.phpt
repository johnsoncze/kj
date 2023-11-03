<?php

declare(strict_types = 1);

namespace App\Tests\Order;

require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

use App\Customer\CustomerRepository;
use App\Delivery\DeliveryRepository;
use App\Delivery\Translation\DeliveryTranslationRepository;
use App\Facades\MailerFacade;
use App\Helpers\Entities;
use App\Order\Order;
use App\Order\OrderCreateFacade;
use App\Order\OrderCreateFacadeException;
use App\Order\OrderCreateFacadeFactory;
use App\Order\Product\Product;
use App\Payment\PaymentRepository;
use App\Payment\Translation\PaymentTranslationRepository;
use App\Product\Production\Time\TimeRepository;
use App\Product\Production\Time\Translation\TimeTranslationRepository;
use App\Product\ProductRepository;
use App\Product\Translation\ProductTranslationRepository;
use App\ShoppingCart\Delivery\ShoppingCartDeliveryRepository;
use App\ShoppingCart\Payment\ShoppingCartPaymentRepository;
use App\ShoppingCart\Product\ShoppingCartProductRepository;
use App\ShoppingCart\ShoppingCartFacade;
use App\ShoppingCart\ShoppingCartFacadeFactory;
use App\ShoppingCart\ShoppingCartRepository;
use App\Tests\BaseTestCase;
use App\Tests\Customer\CustomerTestTrait;
use App\Tests\Delivery\DeliveryTestTrait;
use App\Tests\Delivery\Translation\DeliveryTranslationTestTrait;
use App\Tests\Payment\PaymentTestTrait;
use App\Tests\Payment\Translation\PaymentTranslationTestTrait;
use App\Tests\Product\Production\Time\TimeTestTrait;
use App\Tests\Product\Production\Time\Translation\TimeTranslationTestTrait;
use App\Tests\Product\ProductTestTrait;
use App\Tests\Product\Translation\ProductTranslationTestTrait;
use App\Tests\ShoppingCart\Delivery\ShoppingCartDeliveryTestTrait;
use App\Tests\ShoppingCart\Payment\ShoppingCartPaymentTestTrait;
use App\Tests\ShoppingCart\Product\ShoppingCartProductTestTrait;
use App\Tests\ShoppingCart\ShoppingCartTestTrait;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OrderCreateFacadeTest extends BaseTestCase
{


	use CustomerTestTrait;

	use DeliveryTestTrait;
	use DeliveryTranslationTestTrait;

	use PaymentTestTrait;
	use PaymentTranslationTestTrait;

	use ProductTestTrait;
	use ProductTranslationTestTrait;

	use TimeTestTrait;
	use TimeTranslationTestTrait;

	use ShoppingCartDeliveryTestTrait;
	use ShoppingCartPaymentTestTrait;
	use ShoppingCartTestTrait;
	use ShoppingCartProductTestTrait;

	/** @var CustomerRepository */
	private $customerRepo;

	/** @var DeliveryRepository */
	private $deliveryRepo;

	/** @var DeliveryTranslationRepository */
	private $deliveryTranslationRepo;

	/** @var OrderCreateFacade */
	private $orderCreateFacade;

	/** @var PaymentRepository */
	private $paymentRepo;

	/** @var PaymentTranslationRepository */
	private $paymentTranslationRepo;

	/** @var TimeRepository */
	private $productionTimeRepo;

	/** @var TimeTranslationRepository */
	private $productionTimeTranslationRepo;

	/** @var ProductRepository */
	private $productRepo;

	/** @var ProductTranslationRepository */
	private $productTranslationRepo;

	/** @var ShoppingCartDeliveryRepository */
	private $shoppingCartDeliveryRepo;

	/** @var ShoppingCartFacade */
	private $shoppingCartFacade;

	/** @var ShoppingCartPaymentRepository */
	private $shoppingCartPaymentRepo;

	/** @var ShoppingCartProductRepository */
	private $shoppingCartProductRepo;

	/** @var ShoppingCartRepository */
	private $shoppingCartRepo;



	protected function setUp()
	{
		parent::setUp();
		$this->customerRepo = $this->container->getByType(CustomerRepository::class);

		$this->deliveryRepo = $this->container->getByType(DeliveryRepository::class);
		$this->deliveryTranslationRepo = $this->container->getByType(DeliveryTranslationRepository::class);

		$this->orderCreateFacade = $this->container->getByType(OrderCreateFacadeFactory::class)->create();

		$this->paymentRepo = $this->container->getByType(PaymentRepository::class);
		$this->paymentTranslationRepo = $this->container->getByType(PaymentTranslationRepository::class);

		$this->productionTimeRepo = $this->container->getByType(TimeRepository::class);
		$this->productionTimeTranslationRepo = $this->container->getByType(TimeTranslationRepository::class);
		$this->productRepo = $this->container->getByType(ProductRepository::class);
		$this->productTranslationRepo = $this->container->getByType(ProductTranslationRepository::class);

		$this->shoppingCartDeliveryRepo = $this->container->getByType(ShoppingCartDeliveryRepository::class);
		$this->shoppingCartFacade = $this->container->getByType(ShoppingCartFacadeFactory::class)->create();
		$this->shoppingCartPaymentRepo = $this->container->getByType(ShoppingCartPaymentRepository::class);
		$this->shoppingCartProductRepo = $this->container->getByType(ShoppingCartProductRepository::class);
		$this->shoppingCartRepo = $this->container->getByType(ShoppingCartRepository::class);
	}



	public function test_createFromShoppingCart_unknownCart()
	{
		Assert::exception(function () {
			$this->orderCreateFacade->createFromShoppingCart(11);
		}, OrderCreateFacadeException::class, 'shopping-cart.not.found');
	}



	public function test_createFromShoppingCart_cartWithUnknownCustomer()
	{
		$cart = $this->createTestShoppingCart();
		$cart->setId(NULL);
		$cart->setCustomerId(1);
		$this->saveWithoutForeignKeysCheck($cart, $this->shoppingCartRepo);
		$this->addEntityForRemove($cart, $this->shoppingCartRepo);

		Assert::exception(function () use ($cart) {
			$this->orderCreateFacade->createFromShoppingCart($cart->getId());
		}, OrderCreateFacadeException::class, 'order.message.error.send');
	}



	/**
	 * @dataProvider getCreateFromShoppingCartSuccessDataLoop
	 *
	 * @param $paymentGateway bool
	 * @param $paymentTransfer bool
	 * @param $birthdayCoupon bool
	 * @param $withCustomer bool
	 */
	public function test_createFromShoppingCart_success(bool $paymentGateway, bool $paymentTransfer, bool $birthdayCoupon, bool $withCustomer)
	{
		if ($withCustomer) {
			$customer = $this->createTestCustomer();
			$this->customerRepo->save($customer);
			$this->addEntityForRemove($customer, $this->customerRepo);
		}

		//test production time
		$productionTime = $this->createTestTime();
		$productionTime->setId(NULL);
		$this->productionTimeRepo->save($productionTime);
		$this->addEntityForRemove($productionTime, $this->productionTimeRepo);

		//test production time translation
		$productionTimeTranslation = $this->createTestTimeTranslation();
		$productionTimeTranslation->setId(NULL);
		$productionTimeTranslation->setTimeId($productionTime->getId());
		$this->productionTimeTranslationRepo->save($productionTimeTranslation);

		//test product 1
		$product = $this->createTestProduct();
		$product->setId(NULL);
		$product->setStock(5);
		$product->setPhoto(NULL);
		$this->productRepo->save($product);
		$this->addEntityForRemove($product, $this->productRepo);

		//test product translation 1
		$productTranslation = $this->createTestProductTranslation();
		$productTranslation->setProductId($product->getId());
		$this->productTranslationRepo->save($productTranslation);

		//test product 2
		$product2 = $this->createTestProduct();
		$product2->setId(NULL);
		$product2->setStock(22);
		$product2->setCode('UBLK111');
		$product2->setPhoto(NULL);
		$product2->setExternalSystemId(111);
		$this->productRepo->save($product2);
		$this->addEntityForRemove($product2, $this->productRepo);

		//test product translation 2
		$productTranslation2 = $this->createTestProductTranslation();
		$productTranslation2->setName('Product 2');
		$productTranslation2->setUrl('product-2');
		$productTranslation2->setProductId($product2->getId());
		$this->productTranslationRepo->save($productTranslation2);

		//test delivery
		$delivery = $this->createTestDelivery();
		$delivery->setId(NULL);
		$this->deliveryRepo->save($delivery);
		$this->addEntityForRemove($delivery, $this->deliveryRepo);

		//test delivery translation
		$deliveryTranslation = $this->createTestDeliveryTranslation();
		$deliveryTranslation->setId(NULL);
		$deliveryTranslation->setDeliveryId($delivery->getId());
		$this->deliveryTranslationRepo->save($deliveryTranslation);

		//test payment
		$payment = $this->createTestPayment();
		$payment->setId(NULL);
		$payment->setTransfer($paymentTransfer);
		$payment->setCreditCard($paymentGateway);
		$this->paymentRepo->save($payment);
		$this->addEntityForRemove($payment, $this->paymentRepo);

		//test payment translation
		$paymentTranslation = $this->createTestPaymentTranslation();
		$paymentTranslation->setPaymentId($payment->getId());
		$this->paymentTranslationRepo->save($paymentTranslation);

		//test cart
		$cart = $this->createTestShoppingCart();
		$cart->setId(NULL);
		$cart->setBirthdayCoupon($birthdayCoupon);
		$cart->setCustomerId(isset($customer) ? $customer->getId() : NULL);
		$this->saveWithoutForeignKeysCheck($cart, $this->shoppingCartRepo);
		$this->addEntityForRemove($cart, $this->shoppingCartRepo);

		//test cart payment
		$cartPayment = $this->createTestShoppingCartPayment();
		$cartPayment->setShoppingCartId($cart->getId());
		$cartPayment->setPaymentId($payment->getId());
		$this->shoppingCartPaymentRepo->save($cartPayment);

		//test cart delivery
		$cartDelivery = $this->createTestShoppingCartDelivery();
		$cartDelivery->setShoppingCartId($cart->getId());
		$cartDelivery->setDeliveryId($delivery->getId());
		$this->shoppingCartDeliveryRepo->save($cartDelivery);

		//test cart product 1
		$cartProduct = $this->createTestShoppingCartProduct();
		$cartProduct->setShoppingCartId($cart->getId());
		$cartProduct->setProductId($product->getId());
		$cartProduct->setQuantity(5);

		//test cart product 2
		$cartProduct2 = $this->createTestShoppingCartProduct();
		$cartProduct2->setShoppingCartId($cart->getId());
		$cartProduct2->setProductId($product2->getId());
		$cartProduct2->setQuantity(2);
		$cartProduct2->setProductionTimeId($productionTime->getId());
		$cartProduct2->setProductionTime($productionTime);

		$this->shoppingCartProductRepo->save([$cartProduct, $cartProduct2]);

		$cartDTO = $this->shoppingCartFacade->getDTO($cart->getId());
		$order = $this->orderCreateFacade->createFromShoppingCart($cart->getId());

		Assert::type(Order::class, $order);
		Assert::true(strlen($order->getCode()) === 10);
		Assert::same($cart->isAppliedBirthdayCoupon(), $order->wasAppliedBirthdayDiscount());
		Assert::null($order->getComment());
		Assert::false((bool)$order->getSentToExternalSystem());
		Assert::false((bool)$order->getSentToEETracking());
		Assert::same(Order::NEW_STATE, $order->getState());
		Assert::true(strlen($order->getToken()) === 32);

		isset($customer) ? Assert::same($customer->getId(), (int)$order->getCustomerId()) : Assert::null($order->getCustomerId());
		isset($customer) ? Assert::same($customer->getExternalSystemId(), (int)$order->getCustomerExternalSystemId()) : Assert::null($order->getCustomerExternalSystemId());

		Assert::same($cart->getName(), $order->getCustomerFirstName());
		Assert::same($cart->getLastName(), $order->getCustomerLastName());
		Assert::same($cart->getEmail(), $order->getCustomerEmail());
		Assert::same($cart->getTelephone(), $order->getCustomerTelephone());

		Assert::same($cart->getBillingAddress(), $order->getBillingAddressStreet());
		Assert::same($cart->getBillingCity(), $order->getBillingAddressCity());
		Assert::same($cart->getBillingPostalCode(), $order->getBillingAddressPostcode());
		Assert::same($cart->getBillingCountry(), $order->getBillingAddressCountry());

		Assert::same($cart->getDeliveryFirstName(), $order->getDeliveryAddressFirstName());
		Assert::same($cart->getDeliveryLastName(), $order->getDeliveryAddressLastName());
		Assert::same($cart->getDeliveryCompany(), $order->getDeliveryAddressCompany());
		Assert::same($cart->getDeliveryAddress(), $order->getDeliveryAddressStreet());
		Assert::same($cart->getDeliveryCity(), $order->getDeliveryAddressCity());
		Assert::same($cart->getDeliveryPostalCode(), (int)$order->getDeliveryAddressPostcode());
		Assert::same($cart->getDeliveryCountry(), $order->getDeliveryAddressCountry());

		Assert::same($payment->getId(), (int)$order->getPaymentId());
		Assert::same($payment->getExternalSystemId(), (int)$order->getPaymentExternalSystemId());
		Assert::same($paymentTranslation->getName(), $order->getPaymentName());
		Assert::same($cartPayment->getPrice(), (float)$order->getPaymentPrice());
		Assert::same($cartPayment->getVat(), (float)$order->getPaymentVat());
		Assert::same($payment->isTransfer(), $order->isTransferPayment());
		Assert::same($payment->isRequiredPaymentGateway(), $order->isRequiredPaymentGateway());
		Assert::null($order->getPaymentGatewayTransactionId());
		Assert::null($order->getPaymentGatewayTransactionState());

		Assert::same($delivery->getId(), (int)$order->getDeliveryId());
		Assert::same($delivery->getExternalSystemId(), (int)$order->getDeliveryExternalSystemId());
		Assert::same($deliveryTranslation->getName(), $order->getDeliveryName());
		Assert::same($cartDelivery->getPrice(), (float)$order->getDeliveryPrice());
		Assert::same($cartDelivery->getVat(), (float)$order->getDeliveryVat());
		Assert::null($order->getDeliveryTrackingCode());

		Assert::same($cartDTO->getPrice()->summaryPrice, (float)$order->getSummaryPrice());
		Assert::same($cartDTO->getPrice()->summaryPriceWithoutVat, (float)$order->getSummaryPriceWithoutVat());
		Assert::same($cartDTO->getPrice()->summaryPriceBeforeDiscount, (float)$order->getSummaryPriceBeforeDiscount());
		Assert::same($cartDTO->getPrice()->summaryPriceBeforeDiscountWithoutVat, (float)$order->getSummaryPriceBeforeDiscountWithoutVat());

		Assert::count(2, $order->getProducts());

		/** @var $orderProducts Product[] */
		$orderProducts = Entities::setValueAsKey($order->getProducts(), 'productId');
		$orderProduct = $orderProducts[$product->getId()];
		$orderProduct2 = $orderProducts[$product2->getId()];

		Assert::same($order->getId(), (int)$orderProduct->getOrderId());
		Assert::same($product->getCode(), $orderProduct->getCode());
		Assert::same($product->getExternalSystemId(), (int)$orderProduct->getExternalSystemId());
		Assert::same($productTranslation->getName(), $orderProduct->getName());
		Assert::same($cartProduct->getQuantity(), (int)$orderProduct->getQuantity());
		Assert::same($cartProduct->getDiscount(), (float)$orderProduct->getDiscount());
		Assert::same($cartProduct->getUnitPrice(), (float)$orderProduct->getUnitPrice());
		Assert::same($cartProduct->getUnitPriceWithoutVat(), (float)$orderProduct->getUnitPriceWithoutVat());
		Assert::same($cartProduct->getUnitPriceBeforeDiscount(), (float)$orderProduct->getUnitPriceBeforeDiscount());
		Assert::same($cartProduct->getUnitPriceBeforeDiscountWithoutVat(), (float)$orderProduct->getUnitPriceBeforeDiscountWithoutVat());
		Assert::same($cartProduct->getSummaryPrice(), (float)$orderProduct->getSummaryPrice());
		Assert::same($cartProduct->getSummaryPriceWithoutVat(), (float)$orderProduct->getSummaryPriceWithoutVat());
		Assert::same($cartProduct->getSummaryPriceBeforeDiscount(), (float)$orderProduct->getSummaryPriceBeforeDiscount());
		Assert::same($cartProduct->getSummaryPriceBeforeDiscountWithoutVat(), (float)$orderProduct->getSummaryPriceBeforeDiscountWithoutVat());
		Assert::same($cartProduct->getVat(), (float)$orderProduct->getVat());

		Assert::same($order->getId(), (int)$orderProduct2->getOrderId());
		Assert::same($product2->getCode(), $orderProduct2->getCode());
		Assert::same($product2->getExternalSystemId(), (int)$orderProduct2->getExternalSystemId());
		Assert::same($productTranslation2->getName(), $orderProduct2->getName());
		Assert::same($cartProduct2->getQuantity(), (int)$orderProduct2->getQuantity());
		Assert::same($cartProduct2->getDiscount(), (float)$orderProduct2->getDiscount());
		Assert::same($cartProduct2->getUnitPrice(), (float)$orderProduct2->getUnitPrice());
		Assert::same($cartProduct2->getUnitPriceWithoutVat(), (float)$orderProduct2->getUnitPriceWithoutVat());
		Assert::same($cartProduct2->getUnitPriceBeforeDiscount(), (float)$orderProduct2->getUnitPriceBeforeDiscount());
		Assert::same($cartProduct2->getUnitPriceBeforeDiscountWithoutVat(), (float)$orderProduct2->getUnitPriceBeforeDiscountWithoutVat());
		Assert::same($cartProduct2->getSummaryPrice(), (float)$orderProduct2->getSummaryPrice());
		Assert::same($cartProduct2->getSummaryPriceWithoutVat(), (float)$orderProduct2->getSummaryPriceWithoutVat());
		Assert::same($cartProduct2->getSummaryPriceBeforeDiscount(), (float)$orderProduct2->getSummaryPriceBeforeDiscount());
		Assert::same($cartProduct2->getSummaryPriceBeforeDiscountWithoutVat(), (float)$orderProduct2->getSummaryPriceBeforeDiscountWithoutVat());
		Assert::same($cartProduct2->getVat(), (float)$orderProduct2->getVat());
		Assert::same($productionTime->getSurcharge(), (float)$orderProduct2->getSurchargePercent());
		Assert::same($cartProduct2->getSurcharge(), (float)$orderProduct2->getSurcharge());

		Assert::count(1, MailerFacade::getEmails(TRUE));
	}



	/**
	 * @return array
	 */
	public function getCreateFromShoppingCartSuccessDataLoop() : array
	{
		return [
			[TRUE, FALSE, FALSE, FALSE],
			[TRUE, FALSE, TRUE, TRUE],
			[FALSE, FALSE, FALSE, TRUE],
			[FALSE, TRUE, TRUE, TRUE],
		];
	}
}

(new OrderCreateFacadeTest())->run();