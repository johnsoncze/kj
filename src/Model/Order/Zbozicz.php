<?php

declare(strict_types = 1);

namespace App\Order;

use App\Order\Order;
use Kdyby\Monolog\Logger;

include_once("ZboziKonverze.php");

/**
 * @author Martin Lach <lachmart@gmail.com>
 */
class Zbozicz
{
  /** @var Logger @inject */
  public $logger;

  public function __construct(
              Logger $logger)
  {
      $this->logger = $logger;
  }

    /**
     * Send confirmation of sent order.
     * @param $order Order
     * @return Order
     */
    public function mereniKonverzi(Order $order)
    {

      try {

      // inicializace
      $zbozi = new ZboziKonverze(140289, "VfMrZKeujboEFXvaG6f3x6NYtutHqXFg");

      // testovací režim
      $zbozi->useSandbox(true);

      // nastavení informací o objednávce
      $zbozi->setOrder(array(
          "orderId" => $order->getId(),
          "email" => $order->getCustomerEmail(),
          "deliveryType" => $order->getDeliveryName(),
          "deliveryPrice" => $order->getDeliveryPrice(),
          "otherCosts" => 0,
          "paymentType" =>  $order->getPaymentName(),
      ));

      foreach ($order->getProducts() as $product)
      {
        // přidáni zakoupené položky
        $zbozi->addCartItem(array(
            "itemId" => $product->getProductId(),
            "productName" => $product->getName(),
            "quantity" => $product->getQuantity(),
            "unitPrice" =>  $product->getUnitPrice(),
        ));
      }


      // odeslání
      $zbozi->send();

  } catch (ZboziKonverzeException $e) {
      // zalogování případné chyby
      //error_log("Chyba konverze: " . $e->getMessage());
      $this->logger->addError(sprintf('"Chyba konverze Zbozi.cz: " %s.', $e->getMessage()),array(
          "orderId" => $order->getId(),
          "email" => $order->getCustomerEmail(),
          "deliveryType" => $order->getDeliveryName(),
          "deliveryPrice" => $order->getDeliveryPrice(),
          "paymentType" =>  $order->getPaymentName(),
      ));

  }

}
}
