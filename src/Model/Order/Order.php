<?php

declare(strict_types = 1);

namespace App\Order;

use App\AddDateTrait;
use App\AdminModule\Components\StateChangeForm\IStateObject;
use App\Helpers\Prices;
use App\Helpers\Validators;
use App\Order\Price\AbstractPriceEntity;
use App\Order\Product\Product;
use App\StateTrait;
use Nette\Utils\Random;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="order")
 *
 * @method setCode($code)
 * @method getCode()
 *
 * @method setCustomerId($id)
 * @method getCustomerId()
 * @method setCustomerExternalSystemId($id)
 * @method getCustomerExternalSystemId()
 * @method setCustomerFirstName($name)
 * @method getCustomerFirstName()
 * @method setCustomerLastName($name)
 * @method getCustomerLastName()
 * @method getCustomerEmail()
 * @method setCustomerTelephone($telephone)
 * @method getCustomerTelephone()
 *
 * @method setBillingAddressStreet($street)
 * @method getBillingAddressStreet()
 * @method setBillingAddressCity($city)
 * @method getBillingAddressCity()
 * @method setBillingAddressPostcode($postcode)
 * @method getBillingAddressPostcode()
 * @method setBillingAddressCountry($country)
 * @method getBillingAddressCountry()
 *
 * @method setDeliveryAddressFirstName($name)
 * @method getDeliveryAddressFirstName()
 * @method setDeliveryAddressLastName($name)
 * @method getDeliveryAddressLastName()
 * @method setDeliveryAddressCompany($company)
 * @method getDeliveryAddressCompany()
 * @method setDeliveryAddressStreet($street)
 * @method getDeliveryAddressStreet()
 * @method setDeliveryAddressCity($city)
 * @method getDeliveryAddressCity()
 * @method setDeliveryAddressPostcode($postcode)
 * @method getDeliveryAddressPostcode()
 * @method setDeliveryAddressCountry($country)
 * @method getDeliveryAddressCountry()
 * @method setDeliveryTelephone($telephone)
 * @method getDeliveryTelephone()
 *
 * @method setPaymentId($id)
 * @method getPaymentId()
 * @method setPaymentExternalSystemId($id)
 * @method getPaymentExternalSystemId()
 * @method setPaymentName($name)
 * @method getPaymentName()
 * @method setPaymentPrice($price)
 * @method getPaymentPrice()
 * @method setPaymentVat($vat)
 * @method getPaymentVat()
 *
 * @method setIsRequiredPaymentGateway($bool)
 * @method getIsRequiredPaymentGateway()
 * @method getPaymentGatewayTransactionId()
 *
 * @method setTransferPayment($bool)
 * @method getTransferPayment()
 *
 * @method setDeliveryId($id)
 * @method getDeliveryId()
 * @method setDeliveryExternalSystemId($id)
 * @method getDeliveryExternalSystemId()
 * @method setDeliveryName($name)
 * @method getDeliveryName()
 * @method setDeliveryPrice($price)
 * @method getDeliveryPrice()
 * @method setDeliveryVat($vat)
 * @method getDeliveryVat()
 * @method setDeliveryInformation($information)
 * @method getDeliveryInformation()
 * @method setDeliveryTrackingCode($code)
 * @method getDeliveryTrackingCode()
 *
 * @method setBirthdayDiscount($arg)
 * @method getBirthdayDiscount()
 *
 * @method setComment($comment)
 * @method getComment()
 *
 * @method setProductSummaryPriceWithoutVat($price)
 * @method getProductSummaryPriceWithoutVat()
 * @method setProductSummaryPrice($price)
 * @method getProductSummaryPrice()
 *
 * @method setSentToExternalSystem($bool)
 * @method getSentToExternalSystem()
 *
 * @method getState()
 *
 * @method getAddDate()
 *
 * @method setToken($token)
 *
 * @method setSentToEETracking($arg)
 * @method getSentToEETracking()
 *
 * @method setProducts($products)
 * @method getProducts()
 */
class Order extends AbstractPriceEntity implements IEntity, IStateObject
{


    /** @var string states */
    const NEW_STATE = 'new';
    const ACCEPTED_STATE = 'accepted';
    const READY_FOR_PICK_UP_STATE = 'readyForPickUp';
    const SENT_STATE = 'sent';
    const COMPLETED_STATE = 'completed';
    const CANCELLED_STATE = 'cancelled';
    const RETURNED_STATE = 'returned';
    const STOPPED_STATE = 'stopped';
    const TEST_STATE = 'test';

    /** @var string */
    const NEW_PAYMENT_GATEWAY_STATE = 'new';
    const PAID_PAYMENT_GATEWAY_STATE = 'paid';
    const PENDING_PAYMENT_GATEWAY_STATE = 'pending';
    const CANCELLED_PAYMENT_GATEWAY_STATE = 'cancelled';

    const ORDER_FEED_CACHE_TAG = 'order_feed';

    use AddDateTrait;
    use StateTrait;

    /**
     * @var int|null
     * @Column(name="o_id", key="Primary")
     */
    protected $id;

    /**
     * @var string
     * @Column(name="o_code")
     */
    protected $code;

    /**
     * @var int|null
     * @Column(name="o_customer_id")
     */
    protected $customerId;

    /**
     * @var int|null
     * @Column(name="o_customer_external_system_id")
     */
    protected $customerExternalSystemId;

    /**
     * @var string
     * @Column(name="o_customer_first_name")
     */
    protected $customerFirstName;

    /**
     * @var string
     * @Column(name="o_customer_last_name")
     */
    protected $customerLastName;

    /**
     * @var string
     * @Column(name="o_customer_email")
     */
    protected $customerEmail;

    /**
     * @var string
     * @Column(name="o_customer_telephone")
     */
    protected $customerTelephone;

    /**
     * @var string
     * @Column(name="o_billing_address_street")
     */
    protected $billingAddressStreet;

    /**
     * @var string
     * @Column(name="o_billing_address_city")
     */
    protected $billingAddressCity;

    /**
     * @var int
     * @Column(name="o_billing_address_postcode")
     */
    protected $billingAddressPostcode;

    /**
     * @var string
     * @Column(name="o_billing_address_country")
     */
    protected $billingAddressCountry;

    /**
     * @var string|bool
     * @Column(name="o_birthday_discount")
     */
    protected $birthdayDiscount;

    /**
     * @var string|null
     * @Column(name="o_delivery_address_first_name")
     */
    protected $deliveryAddressFirstName;

    /**
     * @var string|null
     * @Column(name="o_delivery_address_last_name")
     */
    protected $deliveryAddressLastName;

    /**
     * @var string|null
     * @Column(name="o_delivery_address_company")
     */
    protected $deliveryAddressCompany;

    /**
     * @var string|null
     * @Column(name="o_delivery_address_street")
     */
    protected $deliveryAddressStreet;

    /**
     * @var string|null
     * @Column(name="o_delivery_address_city")
     */
    protected $deliveryAddressCity;

    /**
     * @Column(name="o_delivery_address_postcode")
     */
    protected $deliveryAddressPostcode;

    /**
     * @var string|null
     * @Column(name="o_delivery_address_country")
     */
    protected $deliveryAddressCountry;

    /**
     * @Column(name="o_payment_id")
     */
    protected $paymentId;

    /**
     * @var int
     * @Column(name="o_payment_external_system_id")
     */
    protected $paymentExternalSystemId;

    /**
     * @var string
     * @Column(name="o_payment_name")
     */
    protected $paymentName;

    /**
     * @var float
     * @Column(name="o_payment_price")
     */
    protected $paymentPrice = 0.0;

    /**
     * @var float
     * @Column(name="o_payment_vat")
     */
    protected $paymentVat = 0.0;

    /**
     * @var int|null
     * @Column(name="o_transfer_payment")
    */
    protected $transferPayment;

    /**
     * @Column(name="o_delivery_id")
     */
    protected $deliveryId;

    /**
     * @var int
     * @Column(name="o_delivery_external_system_id")
     */
    protected $deliveryExternalSystemId;

    /**
     * @var string
     * @Column(name="o_delivery_name")
     */
    protected $deliveryName;

    /**
     * @var float
     * @Column(name="o_delivery_price")
     */
    protected $deliveryPrice = 0.0;

    /**
     * @var float
     * @Column(name="o_delivery_vat")
     */
    protected $deliveryVat = 0.0;

    /**
     * For detailed information as like pickup point.
     * @var string|null
     * @Column(name="o_delivery_information")
     */
    protected $deliveryInformation;

    /**
     * @var string|null
     * @Column(name="o_delivery_tracking_code")
     */
    protected $deliveryTrackingCode;

    /**
     * @var string
     * @Column(name="o_delivery_telephone")
     */
    protected $deliveryTelephone;

    /**
     * @Column(name="o_is_required_payment_gateway")
     */
    protected $isRequiredPaymentGateway;

    /**
     * @Column(name="o_payment_gateway_transaction_id")
     */
    protected $paymentGatewayTransactionId;

    /**
     * @Column(name="o_payment_gateway_transaction_state")
     */
    protected $paymentGatewayTransactionState;

    /**
     * @var float
     * @Column(name="o_product_summary_price_without_vat")
     */
    protected $productSummaryPriceWithoutVat = 0.0;

    /**
     * @var float
     * @Column(name="o_product_summary_price")
     */
    protected $productSummaryPrice = 0.0;

    /**
     * @var float
     * @Column(name="o_summary_price")
     */
    protected $summaryPrice = 0.0;

    /**
     * @var float
     * @Column(name="o_summary_price_without_vat")
     */
    protected $summaryPriceWithoutVat = 0.0;

    /**
     * @var float
     * @Column(name="o_summary_price_before_discount")
     */
    protected $summaryPriceBeforeDiscount = 0.0;

    /**
     * @var float
     * @Column(name="o_summary_price_before_discount_without_vat")
     */
    protected $summaryPriceBeforeDiscountWithoutVat = 0.0;

    /**
     * @var string|null
     * @Column(name="o_comment")
     */
    protected $comment;

    /**
     * @Column(name="o_sent_to_external_system")
     */
    protected $sentToExternalSystem = FALSE;

    /**
     * @var string
     * @Column(name="o_state")
     */
    protected $state;

    /**
     * Was the order sent to Enhanced Ecommerce tracking?
	 * todo remove and use dataLayer session container
     * @Column(name="o_sent_to_ee_tracking")
     */
    protected $sentToEETracking = FALSE;

    /**
     * @Column(name="o_token")
     */
    protected $token;

    /**
     * @var string
     * @Column(name="o_add_date")
     */
    protected $addDate;

    /**
     * @var Product[]|array
     * @OneToMany(entity="\App\Order\Product\Product")
     */
    protected $products = [];

    /** @var array */
    protected static $states = [
        //after sent order
        self::NEW_STATE => [
            'key' => self::NEW_STATE,
            'translationKey' => 'order.state.new',
            'externalSystemId' => 1,
            'level' => 100,
        ],

        //order is sending to external system with this state only
        //automatically after 60 minutes after sent order
        //or if customer can pay by payment gateway and payment has been sent
        self::ACCEPTED_STATE => [
            'key' => self::ACCEPTED_STATE,
            'translationKey' => 'order.state.accepted',
            'externalSystemId' => 2,
            'level' => 200,
            'waitForPaymentGatewayTransaction' => TRUE,
        ],

        //if is order ready for pick up at store
        self::READY_FOR_PICK_UP_STATE => [
            'key' => self::READY_FOR_PICK_UP_STATE,
            'translationKey' => 'order.state.readyForPickUp',
            'externalSystemId' => 5,
            'waitForPaymentGatewayTransaction' => TRUE,
        ],

        //if order was transferred to the carrier
        self::SENT_STATE => [
            'key' => self::SENT_STATE,
            'translationKey' => 'order.state.sent',
            'externalSystemId' => 6,
            'level' => 300,
            'waitForPaymentGatewayTransaction' => TRUE,
        ],

        //delivered order
        self::COMPLETED_STATE => [
            'key' => self::COMPLETED_STATE,
            'translationKey' => 'order.state.completed',
            'externalSystemId' => 7,
            'level' => 400,
            'waitForPaymentGatewayTransaction' => TRUE,
        ],

        //canceled by customer
        self::CANCELLED_STATE => [
            'key' => self::CANCELLED_STATE,
            'translationKey' => 'order.state.cancelled',
            'externalSystemId' => 8,
            'level' => 500,
        ],

        //customer did not pick up or take from carrier
        self::RETURNED_STATE => [
            'key' => self::RETURNED_STATE,
            'translationKey' => 'order.state.returned',
            'externalSystemId' => 9,
            'level' => 500,
        ],

        //will not be completed
        self::STOPPED_STATE => [
            'key' => self::STOPPED_STATE,
            'translationKey' => 'order.state.stopped',
            'externalSystemId' => 10,
            'level' => 500,
        ],

        //test order
        self::TEST_STATE => [
            'key' => self::TEST_STATE,
            'translationKey' => 'order.state.test',
            'level' => 500,
        ],
    ];

    /** @var array */
    protected static $paymentGatewayTransactionStates = [
        self::NEW_PAYMENT_GATEWAY_STATE => [
            'key' => self::NEW_PAYMENT_GATEWAY_STATE,
            'translationKey' => 'payment.gateway.state.new.label',
        ],
        self::PENDING_PAYMENT_GATEWAY_STATE => [
            'key' => self::PENDING_PAYMENT_GATEWAY_STATE,
            'translationKey' => 'payment.gateway.state.pending.label',
        ],
        self::PAID_PAYMENT_GATEWAY_STATE => [
            'key' => self::PAID_PAYMENT_GATEWAY_STATE,
            'translationKey' => 'payment.gateway.state.paid.label',
        ],
        self::CANCELLED_PAYMENT_GATEWAY_STATE => [
            'key' => self::CANCELLED_PAYMENT_GATEWAY_STATE,
            'translationKey' => 'payment.gateway.state.cancelled.label',
        ],
    ];



    /**
     * Setter for 'customerEmail' property.
     * @param $email string
     * @return self
     * @throws \EntityInvalidArgumentException invalid format of email
     */
    public function setCustomerEmail(string $email) : self
    {
        if (Validators::isEmail($email) !== TRUE) {
            throw new \EntityInvalidArgumentException(sprintf('E-mail \'%s\' does not have valid format.', $email));
        }
        $this->customerEmail = $email;
        return $this;
    }



    /**
     * Setter for property 'state'.
     * @param $state string
     * @return self
     * @throws \InvalidArgumentException unknown state
     * @throws \EntityInvalidArgumentException
     */
    public function setState(string $state) : self
    {
        $states = self::getStates();
        if (!array_key_exists($state, $states)) {
            throw new \InvalidArgumentException(sprintf('Unknown state \'%s\'.', $state));
        }
        if ($this->state === $state) {
            throw new \EntityInvalidArgumentException('Stav je již nastaven.');
        }
        if ($this->isRequiredPaymentGateway() === TRUE && $this->isPaidByPaymentGateway() !== TRUE && array_key_exists($state, self::getWaitForPaymentGatewayTransactionStates()) === TRUE) {
            throw new \EntityInvalidArgumentException('Stav nemůže být nastaven. Objednávka čeká na zaplacení.');
        }
        $this->state = $state;
        return $this;
    }



    /**
     * @return bool
     */
    public function wasSentToExternalSystem() : bool
    {
        return (bool)$this->getSentToExternalSystem();
    }



    /**
     * @param $id int
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function getStateByExternalSystemId(int $id) : array
    {
        $states = self::getStates();
        foreach ($states as $key => $values) {
            $externalSystemId = $values['externalSystemId'] ?? NULL;
            if ($externalSystemId === $id) {
                return $values;
            }
        }
        throw new \InvalidArgumentException(sprintf('Neznáme externí id \'%d\' stavu objednávky.', $id));
    }



    /**
     * @return array
     */
    public static function getStates() : array
    {
        return self::$states;
    }



    /**
     * @return bool
     */
    public function wasAppliedBirthdayDiscount() : bool
    {
        return (bool)$this->birthdayDiscount;
    }



    /**
     * @return bool
     */
    public function wasSentToEETracking() : bool
    {
        return (bool)$this->getSentToEETracking();
    }



    /**
     * @return float
     */
    public function getProductSummaryPriceVat() : float
    {
        return $this->getProductSummaryPrice() - $this->getProductSummaryPriceWithoutVat();
    }



    /**
     * @return float
     */
    public function countProductSummaryPrice() : float
    {
        return $this->getSummaryPrice() - $this->getDeliveryPrice() - $this->getPaymentPrice();
    }



    /**
     * @return float
     */
    public function countProductSummaryPriceWithoutVat() : float
    {
        return $this->getSummaryPriceWithoutVat() - $this->getDeliveryPriceWithoutVat() - $this->getPaymentPriceWithoutVat();
    }



    /**
     * @return float
     */
    public function getDeliveryPriceWithoutVat() : float
    {
        return Prices::toBeforePercent($this->getDeliveryPrice(), $this->getDeliveryVat());
    }



    /**
     * @return float
     */
    public function getPaymentPriceWithoutVat() : float
    {
        return Prices::toBeforePercent($this->getPaymentPrice(), $this->getPaymentVat());
    }



    /**
     * @param $id string
     * @return self
     * @throws \EntityInvalidArgumentException
     */
    public function setPaymentGatewayTransactionId(string $id)
    {
        $maxLength = 14;
        $length = strlen($id);
        if ($length > $maxLength) {
            throw new \EntityInvalidArgumentException(sprintf('Max length of id is \'%d\'. Length \'%d\' inserted.', $maxLength, $length));
        }
        $this->paymentGatewayTransactionId = $id;
        return $this;
    }



    /**
     * @return bool
     */
    public function isRequiredPaymentGateway() : bool
    {
        return (bool)$this->getIsRequiredPaymentGateway();
    }



    /**
     * @return bool
     */
    public function isPaidByPaymentGateway() : bool
    {
        return $this->getPaymentGatewayTransactionState() === self::PAID_PAYMENT_GATEWAY_STATE;
    }



    /**
     * @return string
     */
    public function getToken(): string
    {
        if (!$this->token) {
            $this->setToken(Random::generate(32));
        }
        return $this->token;
    }




    /**
     * @return bool
    */
    public function isTransferPayment() : bool
    {
        return (bool)$this->getTransferPayment();
    }



    /**
     * @return float
     */
    public function getDeliveryAndPaymentSummaryPriceWithoutVat() : float
    {
        return $this->getDeliveryPriceWithoutVat() + $this->getPaymentPriceWithoutVat();
    }



    /**
     * @return string
     */
    public function getFormattedBillingAddressPostcode() : string
    {
        $postCode = (string)$this->getBillingAddressPostcode();
        return substr_replace($postCode, ' ', 3, 0);
    }



    /**
     * @return string|null
     */
    public function getFormattedDeliveryAddressPostcode()
    {
        $postCode = (string)$this->getDeliveryAddressPostcode();
        return $postCode ? substr_replace($postCode, ' ', 3, 0) : NULL;
    }



    /**
     * @param $state string
     * @return self
     * @throws \EntityInvalidArgumentException
     */
    public function setPaymentGatewayTransactionState(string $state) : self
    {
        $state = strtolower($state);
        $states = self::getPaymentGatewayTransactionStates();
        if (array_key_exists($state, $states) === FALSE) {
            throw new \EntityInvalidArgumentException(sprintf('Unknown state \'%s\'.', $state));
        }
        $this->paymentGatewayTransactionState = $state;
        return $this;
    }



    /**
     * @return string
     * @throws \InvalidArgumentException missing transaction state
    */
    public function getPaymentGatewayEmailMethodName() : string
    {
        $state = $this->getPaymentGatewayTransactionState();
        if ($state === NULL) {
            throw new \InvalidArgumentException('Missing transaction state.');
        }
        return 'paymentGateway' . ucfirst($state);
    }



    /**
     * @param $required bool
     * @return string|null
     * @throws \InvalidArgumentException
    */
    public function getPaymentGatewayTransactionState(bool $required = FALSE)
    {
        $state = $this->paymentGatewayTransactionState;
        if ($required === TRUE && $state === NULL) {
            throw new \InvalidArgumentException('Missing state.');
        }
        return $state;
    }



    /**
     * @return array
     * @throws \InvalidArgumentException
    */
    public function getPaymentGatewayTransactionStateValues() : array
    {
        $state = $this->getPaymentGatewayTransactionState(TRUE);
        $states = self::getPaymentGatewayTransactionStates();
        $values = $states[$state] ?? [];
        if (!$values) {
            throw new \InvalidArgumentException(sprintf('Missing values for state \'%s\'.', $state));
        }
        return $values;
    }

    public function isGatewayPaymentAvailable(): bool
    {
        return $this->isRequiredPaymentGateway() === true && $this->isPaidByPaymentGateway() !== true;
    }

    /**
     * @return array
     */
    public static function getPaymentGatewayTransactionStates() : array
    {
        return self::$paymentGatewayTransactionStates;
    }



    /**
     * @return array
    */
    public static function getWaitForPaymentGatewayTransactionStates() : array
    {
        $states = [];
        $stateList = self::getStates();
        foreach ($stateList as $key => $state){
            if (isset($state['waitForPaymentGatewayTransaction'])) {
                $states[$key] = $state;
            }
        }
        return $states;
    }
}