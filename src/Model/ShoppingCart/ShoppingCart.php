<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

use App\AddDateTrait;
use App\BaseEntity;
use App\Customer\Customer;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Exceptions\EntityException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="shopping_cart")
 *
 * @method setIpAddress($address)
 * @method getIpAddress()
 * @method setCustomerId($id)
 * @method getCustomerId()
 * @method setName($name)
 * @method getName()
 * @method setFirstName($name)
 * @method getFirstName()
 * @method setLastName($name)
 * @method getLastName()
 * @method setEmail($email)
 * @method getEmail()
 * @method setTelephone($telephone)
 * @method getTelephone()
 * @method setDeliveryFirstName($name)
 * @method getDeliveryFirstName()
 * @method setDeliveryLastName($name)
 * @method getDeliveryLastName()
 * @method setDeliveryCompany($company)
 * @method getDeliveryCompany()
 * @method setDeliveryAddress($address)
 * @method getDeliveryAddress()
 * @method setDeliveryCity($city)
 * @method getDeliveryCity()
 * @method setDeliveryPostalCode($code)
 * @method getDeliveryPostalCode()
 * @method setDeliveryCountry($country)
 * @method getDeliveryCountry()
 * @method setDeliveryInformation($information)
 * @method getDeliveryInformation()
 * @method setBillingName($name)
 * @method getBillingName()
 * @method setBillingAddress($address)
 * @method getBillingAddress()
 * @method setBillingCity($city)
 * @method getBillingCity()
 * @method setBillingPostalCode($code)
 * @method getBillingPostalCode()
 * @method setBillingCountry($country)
 * @method getBillingCountry()
 * @method setBillingIn($in)
 * @method getBillingIn()
 * @method setBillingVatId($id)
 * @method getBillingVatId()
 * @method setBillingTelephone($telephone)
 * @method getBillingTelephone()
 * @method setBillingEmail($email)
 * @method getBillingEmail()
 * @method setBillingBankAccount($account)
 * @method getBillingBankAccount()
 * @method getComment()
 * @method setBirthdayCoupon(bool $bool)
 * @method getHash()
 */
class ShoppingCart extends BaseEntity implements IEntity
{

    /** @var int */
    const MAX_LENGTH_COMMENT = 500;

    /** @var string */
    const SESSION_SECTION = 'shopping_cart';


    use AddDateTrait;


    /**
     * @Column(name="sc_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="sc_ip_address")
     */
    protected $ipAddress;

    /**
     * @Column(name="sc_customer_id")
     */
    protected $customerId;

    /**
     * @Column(name="sc_name")
     */
    protected $name;

    /**
     * @Column(name="sc_first_name")
     */
    protected $firstName;

    /**
     * @Column(name="sc_last_name")
     */
    protected $lastName;

    /**
     * @Column(name="sc_email")
     */
    protected $email;

    /**
     * @Column(name="sc_telephone")
     */
    protected $telephone;

    /**
     * @Column(name="sc_delivery_first_name")
     */
    protected $deliveryFirstName;

    /**
     * @Column(name="sc_delivery_last_name")
     */
    protected $deliveryLastName;

    /**
     * @Column(name="sc_delivery_company")
     */
    protected $deliveryCompany;

    /**
     * @Column(name="sc_delivery_address")
     */
    protected $deliveryAddress;

    /**
     * @Column(name="sc_delivery_city")
     */
    protected $deliveryCity;

    /**
     * @Column(name="sc_delivery_postal_code")
     */
    protected $deliveryPostalCode;

    /**
     * @Column(name="sc_delivery_country")
     */
    protected $deliveryCountry;

    /**
     * @Column(name="sc_delivery_information")
     */
    protected $deliveryInformation;

    /**
     * @Column(name="sc_billing_name")
     */
    protected $billingName;

    /**
     * @Column(name="sc_billing_address")
     */
    protected $billingAddress;

    /**
     * @Column(name="sc_billing_city")
     */
    protected $billingCity;

    /**
     * @Column(name="sc_billing_postal_code")
     */
    protected $billingPostalCode;

    /**
     * @Column(name="sc_billing_country")
     */
    protected $billingCountry;

    /**
     * @Column(name="sc_billing_in")
     */
    protected $billingIn;

    /**
     * @Column(name="sc_billing_vat_id")
     */
    protected $billingVatId;

    /**
     * @Column(name="sc_billing_telephone")
     */
    protected $billingTelephone;

    /**
     * @Column(name="sc_billing_email")
     */
    protected $billingEmail;

    /**
     * @Column(name="sc_billing_bank_account")
     */
    protected $billingBankAccount;

    /**
     * @Column(name="sc_comment")
     */
    protected $comment;

    /**
     * @Column(name="sc_birthday_coupon")
     */
    protected $birthdayCoupon;

    /**
     * @Column(name="sc_add_date")
     */
    protected $addDate;

    /**
     * @Column(name="sc_hash")
     */
    protected $hash;



    /**
     * @param string $hash
     * @return ShoppingCart
     * @throws EntityException
     */
    public function setHash(string $hash) : self
    {
        if ($this->hash !== NULL) {
            throw new EntityException('You can not change hash of shopping cart.');
        }
        $this->hash = $hash;
        return $this;
    }



    /**
     * @param $comment string|null
     * @return self
     */
    public function setComment(string $comment = NULL) : self
    {
        $this->comment = $comment !== NULL ? mb_substr($comment, 0, self::MAX_LENGTH_COMMENT, 'UTF-8') : $comment;
        return $this;
    }



    /**
     * @return bool
     * @throws EntityException
     */
    public function isAppliedBirthdayCoupon() : bool
    {
        if ($this->birthdayCoupon === NULL) {
            throw new EntityException('You must first set birthday coupon property.');
        }
        return (bool)$this->birthdayCoupon;
    }



    /**
     * @return bool
     */
    public function getBirthdayCoupon() : bool
    {
        if ($this->birthdayCoupon === NULL) {
            return FALSE;
        }
        return (bool)$this->birthdayCoupon;
    }



    /**
	 * @return float
    */
    public function getDiscount() : float
	{
		$birthdayDiscount = $this->isAppliedBirthdayCoupon() ? ShoppingCartDiscount::BIRTHDAY_COUPON_DISCOUNT : 0;
		$customerDiscount = $this->getCustomerId() !== NULL ? Customer::DISCOUNT : 0;
		return $birthdayDiscount ?: $customerDiscount;
	}
}