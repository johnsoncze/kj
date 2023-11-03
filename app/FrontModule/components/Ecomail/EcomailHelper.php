<?php

namespace App\FrontModule\Components\Ecomail;

use App\Order\Order;
use Ecomail\Ecomail;
use Nette\SmartObject;
use Tracy\Debugger;

class EcomailHelper extends Ecomail
{

	use SmartObject;

//    CONST APIKEY = '6058d8777c7606058d8777c762';
//    CONST DATABASEID =  '1';

	protected $databaseId;

	public function __construct(string $databaseId, string $apiKey)
	{
		$this->databaseId = $databaseId;
		parent::__construct($apiKey);
	}

	public function sendOrder(Order $order)
	{
			$subscriber = [
				'trigger_autoresponders' => false,
				'update_existing' => true,
				'resubscribe' => false,
				'skip_confirmation' => true,
			];
			$subscriber['subscriber_data'] = [
				'name' => $order->getCustomerFirstName(),
				'surname' => $order->getCustomerLastName(),
				'email' => $order->getCustomerEmail(),
				'street' => $order->getBillingAddressStreet(),
				'city' => $order->getBillingAddressCity(),
				'zip' => $order->getBillingAddressPostcode(),
				'phone' => $order->getCustomerTelephone(),
                'vokativ' => '',
                'vokativ_s' => '',
                'company' => '',
                'country' => $order->getBillingAddressCountry(),
                'pretitle' => '',
                'surtitle' => '',
                'birthday' => '',
                'custom_fields' => [],
			];
//			if(Debugger::$productionMode) {
				Debugger::log($this->addSubscriber($this->databaseId, $subscriber['subscriber_data']), 'ecomail');
//			}

//			$transaction = [
//				'order_id' => $order->getId(),
//				'email' => $order->getCustomerEmail(),
//				'shop' => 'jk.cz',
//				'amount' => $order->getSummaryPrice(),
//				'shipping' => $order->getDeliveryPrice(),
//				'city' => $order->getBillingAddressCity(),
//				'timestamp' => $order->getAddDate()->getTimestamp(),
//			];
//			$transactionItems = [];
//
//			foreach ($order->getProducts() as $orderItem) {
//				$transactionItem = [
//					"code" => $orderItem->getProductId(),
//					"title" => $orderItem->getName(),
////					"category" => $orderItem->getStock()->getVariant()->getProduct()->getCategory()->getTitle(),
//					"price" => $orderItem->getUnitPrice(),
//					"amount" => $orderItem->getQuantity(),
//				];
//				$transactionItems[] = $transactionItem;
//			}
//
//			$body = [
//				'transaction' => $transaction,
//				'transaction_items' => $transactionItems,
//			];
////			if(Debugger::$productionMode) {
//				Debugger::log($this->createNewTransaction($body), 'ecomail');
////			}
	}

	public function addNewsletterSubscribe($email)
	{

//		$subscriber = [
//			'trigger_autoresponders' => false,
//			'update_existing' => true,
//			'resubscribe' => true,
//			'skip_confirmation' => true,
//		];
//		$subscriber['subscriber_data'] = $data;

        $data['email'] = $email;
        $data['name'] = '';
		$data['surname'] = '';
        $data['vokativ'] = '';
        $data['vokativ_s'] = '';
        $data['company'] = '';
        $data['city'] = '';
        $data['street'] = '';
        $data['zip'] = '';
        $data['country'] = '';
        $data['phone'] = '';
        $data['pretitle'] = '';
        $data['surtitle'] = '';
        $data['birthday'] = '';
        $data['custom_fields']['OSLOVENI'] = 'Dobrý den,';

        //$this->addSubscriber($this->databaseId, $data);

        /*
		if(Debugger::$productionMode) {
			Debugger::log($this->addSubscriber($this->databaseId, $data), 'ecomail');
		}
        */
	}

	public function updateUser($userEntity)
	{
		if ($userEntity->getNewsletter()) {
			$data = [
				'name' => $userEntity->getName(),
				'surname' => $userEntity->getSurname(),
				'email' => $userEntity->getEmail(),
				'street' => $userEntity->getStreet() . ' ' . $userEntity->getStreetNumber(),
				'city' => $userEntity->getCity(), 'zip' => $userEntity->getZip(),
				'phone' => $userEntity->getPhone(),
				'gender' => $userEntity->getGender() == 1 ? 'female' : 'male',
			];
			$this->addNewsletterSubscribe($data);
		} else {
			if(Debugger::$productionMode) {
				Debugger::log($this->removeSubscriber($this->databaseId, ['email' => $userEntity->getEmail()]), 'ecomail');
			}
		}
	}

}