<?php

declare(strict_types=1);

namespace App\Order;

use App\Customer\CustomerRepository;
use App\Order\Email\SendEmail;
use App\Order\Product\Parameter\ParameterFactory;
use App\Order\Product\Parameter\ParameterRepository;
use App\Order\Product\ProductFactory;
use App\Order\Product\ProductRepository;
use App\Order\Heureka;
use App\Order\Zbozicz;
use App\Product\ProductRepository as CatalogProductRepository;
use App\ShoppingCart\ShoppingCartFacadeException;
use App\ShoppingCart\ShoppingCartFacadeFactory;
use App\ShoppingCart\ShoppingCartRepository;
use Kdyby\Monolog\Logger;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OrderCreateFacade
{


    /** @var CatalogProductRepository */
    private $catalogProductRepo;

    /** @var CustomerRepository */
    private $customerRepo;

    /** @var Logger */
    private $logger;

    /** @var OrderFactory */
    private $orderFactory;

    /** @var ProductFactory */
    private $orderProductFactory;

    /** @var ParameterFactory */
    private $orderProductParameterFactory;

    /** @var ParameterRepository */
    private $orderProductParameterRepo;

    /** @var OrderRepository */
    private $orderRepo;

    /** @var ProductRepository */
    private $orderProductRepo;

    /** @var SendEmail */
    private $sendEmail;

    /** @var ShoppingCartFacadeFactory */
    private $shoppingCartFacadeFactory;

    /** @var ShoppingCartRepository */
    private $shoppingCartRepo;

    /** @var ITranslator */
    private $translator;

    /** @var Heureka */
    private $heureka;

    /** @var Zbozicz */
    private $zbozicz;


    public function __construct(
        CatalogProductRepository $catalogProductRepo,
        CustomerRepository $customerRepository,
        ITranslator $translator,
        Logger $logger,
        OrderFactory $orderFactory,
        OrderRepository $orderRepository,
        ParameterFactory $parameterFactory,
        ParameterRepository $parameterRepository,
        ProductFactory $productFactory,
        ProductRepository $productRepo,
        SendEmail $sendEmail,
        ShoppingCartFacadeFactory $shoppingCartFacadeFactory,
        ShoppingCartRepository $shoppingCartRepository,
        Heureka $heureka,
        Zbozicz $zbozicz
    ) {
        $this->catalogProductRepo = $catalogProductRepo;
        $this->customerRepo = $customerRepository;
        $this->logger = $logger;
        $this->orderFactory = $orderFactory;
        $this->orderProductParameterFactory = $parameterFactory;
        $this->orderProductParameterRepo = $parameterRepository;
        $this->orderRepo = $orderRepository;
        $this->orderProductRepo = $productRepo;
        $this->orderProductFactory = $productFactory;
        $this->sendEmail = $sendEmail;
        $this->shoppingCartFacadeFactory = $shoppingCartFacadeFactory;
        $this->shoppingCartRepo = $shoppingCartRepository;
        $this->translator = $translator;
        $this->heureka = $heureka;
        $this->zbozicz = $zbozicz;
    }


    /**
     * @param $cartId int
     * @return Order
     * @throws OrderCreateFacadeException
     */
    public function createFromShoppingCart(int $cartId, bool $noHeureka = true): Order
    {
        $cartFacade = $this->shoppingCartFacadeFactory->create();

        try {
            $cartDTO = $cartFacade->getDTO($cartId);
            if ($cartDTO->getEntity()->getCustomerId()) {
                $customer = $this->customerRepo->getOneAllowedById($cartDTO->getEntity()->getCustomerId());
            }
            $customer = $customer ?? $this->customerRepo->findOneByEmail($cartDTO->getEntity()->getEmail());

            //order
            $order = $this->orderFactory->createByShoppingCart($customer ?? null, $cartDTO);
            $this->orderRepo->save($order);

            //products
            $products = [];
            $cartProducts = $cartDTO->getProducts();
            foreach ($cartProducts as $product) {
                $orderProduct = $this->orderProductFactory->createByCartProduct($order, $product);
                $products[$orderProduct->getProductId()] = $orderProduct;

                //subtract from stock
                if (!$product->getProductionTimeId()) {
                    $catalogProduct = $product->getCatalogProduct(true);
                    $catalogProduct->subtractFromStock($product->getQuantity());
                    $this->catalogProductRepo->save($catalogProduct);
                }
            }
            $this->orderProductRepo->save($products);

            //save product parameters
            $productParameters = [];
            foreach ($cartProducts as $cartProduct) {
                $productDTO = $cartDTO->getProductDTOByProductId($cartProduct->getProductId());
                $parameters = $productDTO->getVisibleParameters();
                foreach ($parameters as $cartProductParameter) {
                    $orderProduct = $products[$cartProduct->getProductId()];
                    $productParameters[] = $this->orderProductParameterFactory->create($orderProduct, $cartProductParameter->getGroup(), $cartProductParameter);
                }
            }
            $productParameters ? $this->orderProductParameterRepo->save($productParameters) : null;

            $order->setProducts($products);
            if (isset($customer) && $cartDTO->getEntity()->isAppliedBirthdayCoupon() === true) {
                $customer->setBirthdayCoupon(false);
								$customer->setBirthdayCouponLastUse(date('Y-m-d H:i:s'));
                $this->customerRepo->save($customer);
            }


            $this->shoppingCartRepo->remove($cartDTO->getEntity()); //delete cart


//            email
            $orderFromStorage = $this->orderRepo->getOneById($order->getId());
            $this->sendEmail->confirmation($orderFromStorage);
            if (!$noHeureka) {
                $this->heureka->overenoZakazniky($orderFromStorage);
            }
            $this->zbozicz->mereniKonverzi($orderFromStorage);
            return $order;
        } catch (ShoppingCartFacadeException $exception) {
            throw new OrderCreateFacadeException($exception->getMessage());
        } catch (\XException $exception) {
            $this->logger->addError($exception->getMessage());
            throw new OrderCreateFacadeException($this->translator->translate('order.message.error.send'));
        }
    }
}
