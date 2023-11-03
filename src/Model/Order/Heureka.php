<?php

declare(strict_types=1);

namespace App\Order;

use App\Order\Order;
use Kdyby\Monolog\Logger;


/**
 * @author Martin Lach <lachmart@gmail.com>
 */
class Heureka
{

    /** @var Logger */
    public $logger;

    /** @var string */
    public $heurekaApiKey;

    public function __construct(Logger $logger, string $heurekaApiKey)
    {
        $this->logger = $logger;
        $this->heurekaApiKey = $heurekaApiKey;
    }

    /**
     * Send confirmation of sent order.
     * @param $order Order
     * @return Order
     */
    public function overenoZakazniky(Order $order)
    {
        bdump($this->heurekaApiKey);


        //return null;
        try {
            $options = [
                'service' => \Heureka\ShopCertification::HEUREKA_CZ,
            ];
            $shopCertification = new \Heureka\ShopCertification($this->heurekaApiKey, $options);
            // Set customer email - it is MANDATORY.
            $shopCertification->setEmail($order->getCustomerEmail());
            // Set order ID - it helps you track your customers' orders in Heureka shop administration.
            $shopCertification->setOrderId($order->getId());
            foreach ($order->getProducts() as $product) // Add products using ITEM_ID (your products ID)
            {
                $shopCertification->addProductItemId($product->getCode());
            }
            // And finally send the order to our service.
            $shopCertification->logOrder();
            // Everything went well - we are done here.
            // You can redirect the customer to some nice page and thank him for the order. :-)
        } catch (\Heureka\ShopCertification\Exception $e) {
            // Something unexpected happened.
            // We can print the message for debug purposes only,
            // DO NOT ever do that on your production environment.
            //var_dump($e->getMessage());
            $this->logger->addError(sprintf('"Chyba konverze Heureka: " %s.', $e->getMessage()), [
                "orderId" => $order->getId(),
                "email" => $order->getCustomerEmail(),
                "deliveryType" => $order->getDeliveryName(),
                "deliveryPrice" => $order->getDeliveryPrice(),
                "paymentType" => $order->getPaymentName(),
            ]);
        }
    }
}
