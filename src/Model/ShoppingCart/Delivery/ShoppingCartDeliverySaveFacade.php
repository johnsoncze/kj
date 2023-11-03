<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Delivery;

use App\Delivery\Delivery;
use App\Delivery\DeliveryAllowedRepositoryFactory;
use App\Delivery\DeliveryNotFoundException;
use App\ShoppingCart\ShoppingCart;
use App\ShoppingCart\ShoppingCartNotFoundException;
use App\ShoppingCart\ShoppingCartRepositoryFactory;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ShoppingCartDeliverySaveFacade
{


    /** @var ShoppingCartDeliveryFactory */
    protected $shoppingCartDeliveryFactory;

    /** @var ShoppingCartRepositoryFactory */
    protected $shoppingCartRepoFactory;

    /** @var DeliveryAllowedRepositoryFactory */
    protected $deliveryRepositoryFactory;

    /** @var ShoppingCartDeliveryRepositoryFactory */
    protected $shoppingCartDeliveryRepoFactory;

    /** @var ITranslator $translator */
    protected $translator;



    public function __construct(ShoppingCartDeliveryFactory $shoppingCartDeliveryFactory,
                                ShoppingCartRepositoryFactory $shoppingCartRepoFactory,
                                DeliveryAllowedRepositoryFactory $deliveryRepositoryFactory,
                                ShoppingCartDeliveryRepositoryFactory $shoppingCartDeliveryRepoFactory,
                                ITranslator $translator)
    {
        $this->shoppingCartDeliveryFactory = $shoppingCartDeliveryFactory;
        $this->shoppingCartRepoFactory = $shoppingCartRepoFactory;
        $this->deliveryRepositoryFactory = $deliveryRepositoryFactory;
        $this->shoppingCartDeliveryRepoFactory = $shoppingCartDeliveryRepoFactory;
        $this->translator = $translator;
    }



    /**
     * @param int $shoppingCartId
     * @param int $deliveryId
     * @return ShoppingCartDelivery
     * @throws ShoppingCartDeliverySaveFacadeException
     */
    public function save(int $shoppingCartId, int $deliveryId) : ShoppingCartDelivery
    {
        $shoppingCartDeliveryRepo = $this->shoppingCartDeliveryRepoFactory->create();

        try {
            $shoppingCart = $this->getShoppingCart($shoppingCartId);
            $delivery = $this->getDelivery($deliveryId);
        } catch (DeliveryNotFoundException $exception) {
            throw new ShoppingCartDeliverySaveFacadeException($this->translator->translate('shopping-cart.delivery.not.found'));
        } catch (ShoppingCartNotFoundException $exception) {
            throw new ShoppingCartDeliverySaveFacadeException($this->translator->translate('shopping-cart.action.failed'));
        }

        try {
            //if any exists, check if the same
            $shoppingCartDelivery = $shoppingCartDeliveryRepo->getOneByShoppingCartId($shoppingCartId);
            $shoppingCartDelivery->setAddDate(new \DateTime());
            if ((int)$shoppingCartDelivery->getDeliveryId() !== $deliveryId) { //update delivery
                $shoppingCartDeliveryId = $shoppingCartDelivery->getId();
                $shoppingCartDelivery = $this->shoppingCartDeliveryFactory->create($shoppingCart, $delivery);
                $shoppingCartDelivery->setId($shoppingCartDeliveryId);
                $shoppingCartDeliveryRepo->save($shoppingCartDelivery);
            }
        } catch (ShoppingCartDeliveryNotFoundException $exception) {
            //create new
            $shoppingCartDelivery = $this->shoppingCartDeliveryFactory->create($shoppingCart, $delivery);
            $shoppingCartDelivery->setAddDate(new \DateTime());
            $shoppingCartDeliveryRepo->save($shoppingCartDelivery);
        }

        return $shoppingCartDelivery;
    }



    /**
     * @param int $id
     * @return ShoppingCart
     * @throws ShoppingCartNotFoundException
     */
    protected function getShoppingCart(int $id) : ShoppingCart
    {
        $shoppingCartRepo = $this->shoppingCartRepoFactory->create();
        return $shoppingCartRepo->getOneById($id, $this->translator);
    }



    /**
     * @param int $id
     * @return Delivery
     * @throws DeliveryNotFoundException
     */
    protected function getDelivery(int $id) : Delivery
    {
        $deliveryRepo = $this->deliveryRepositoryFactory->create();
        return $deliveryRepo->getOneById($id);
    }
}