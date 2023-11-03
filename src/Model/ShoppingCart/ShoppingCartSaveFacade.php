<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

use App\Customer\CustomerNotFoundException;
use App\Customer\CustomerRepositoryFactory;
use App\Helpers\Entities;
use App\ShoppingCart\Product\Discount;
use App\ShoppingCart\Product\Merger;
use App\ShoppingCart\Product\ShoppingCartProductRepository;
use Kdyby\Translation\ITranslator;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Http\Session;
use Nette\InvalidArgumentException;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartSaveFacade extends NObject
{


    /** @var string */
    const CHANGED = 'changed';
    const MERGED = 'merged';
    const MERGED_PRODUCTS = 'merged_products';

    /** @var BirthdayDiscount */
    protected $birthdayCoupon;

    /** @var ShoppingCartProductRepository */
    protected $cartProductRepo;

    /** @var CustomerRepositoryFactory */
    protected $customerRepositoryFactory;

    /** @var Discount */
    protected $discount;

    /** @var Merger */
    protected $productMerger;

    /** @var Session */
    protected $session;

    /** @var ShoppingCartRepositoryFactory */
    protected $shoppingCartRepoFactory;

    /** @var ITranslator */
    protected $translator;



    public function __construct(BirthdayDiscount $birthdayDiscount,
                                CustomerRepositoryFactory $customerRepositoryFactory,
                                Discount $discount,
                                Merger $merger,
                                Session $session,
                                ShoppingCartProductRepository $shoppingCartProductRepository,
                                ShoppingCartRepositoryFactory $shoppingCartRepoFactory,
                                ITranslator $translator)
    {
        $this->birthdayCoupon = $birthdayDiscount;
        $this->customerRepositoryFactory = $customerRepositoryFactory;
        $this->cartProductRepo = $shoppingCartProductRepository;
        $this->discount = $discount;
        $this->productMerger = $merger;
        $this->session = $session;
        $this->shoppingCartRepoFactory = $shoppingCartRepoFactory;
        $this->translator = $translator;
    }



    /**
     * Find or create shopping cart.
     * @param $customerId int|null
     * @return ShoppingCart
     * @throws ShoppingCartSaveFacadeException
     * @throws InvalidArgumentException
     */
    public function get(int $customerId = NULL)
    {
        $shoppingCart = $this->find($customerId);
        $shoppingCart = $shoppingCart ?? $this->saveNew($customerId);
        if ($customerId === NULL) {
            $this->session->getSection(ShoppingCart::SESSION_SECTION)->id = $shoppingCart->getId();
        }
        return $shoppingCart;
    }



    /**
     * Find shopping cart if exists.
     * @param $customerId int|null
     * @return ShoppingCart|null
     * @throws InvalidArgumentException
     */
    public function find(int $customerId = NULL)
    {
        $shoppingCartRepo = $this->shoppingCartRepoFactory->create();

        try {
            if ($customerId !== NULL) {
                return $shoppingCartRepo->getOneByCustomerId($customerId);
            }
            $session = $this->session->getSection(ShoppingCart::SESSION_SECTION);
            if (isset($session->id)) {
                return $shoppingCartRepo->getOneById($session->id, $this->translator);
            }
        } catch (ShoppingCartNotFoundException $exception) {
            //nothing..
        }

        return NULL;
    }



    /**
     * @param int|NULL $customerId
     * @return ShoppingCart
     * @throws ShoppingCartSaveFacadeException
     */
    public function saveNew(int $customerId = NULL) : ShoppingCart
    {
        try {
            $cart = new ShoppingCart();
            $cart->setBirthdayCoupon(FALSE);
            $cart->setAddDate(new \DateTime());

            if ($customerId !== NULL) {
                $customerRepo = $this->customerRepositoryFactory->create();
                $customer = $customerRepo->getOneAllowedById($customerId);
                $cart->setCustomerId($customer->getId());
								
								if ($customer->getBirthdayCoupon()) {
										$this->setBirthdayDiscount($cart, true);
								}
            }

            $this->setIpAddress($cart);
            $this->setHash($cart);
            $cartRepo = $this->shoppingCartRepoFactory->create();
            $cartRepo->save($cart);

            return $cart;
        } catch (CustomerNotFoundException $exception) {
            throw new ShoppingCartSaveFacadeException($this->translator->translate(sprintf('%s.action.failed', ShoppingCartTranslation::getFileName())));
        } catch (UniqueConstraintViolationException $exception) {
            throw new ShoppingCartSaveFacadeException($this->translator->translate(sprintf('%s.action.failed', ShoppingCartTranslation::getFileName())));
        }
    }



    /**
     * @param int $cartId
     * @param string|NULL $name
     * @param $firstName string|null
     * @param $lastName string|null
     * @param string|NULL $email
     * @param string|NULL $telephone
     * @param $deliveryFirstName string|null
     * @param $deliveryLastName string|null
     * @param $deliveryCompany string|null
     * @param string|NULL $deliveryAddress
     * @param string|NULL $deliveryCity
     * @param string|NULL $deliveryPostalCode
     * @param string|NULL $deliveryCountry
     * @param $deliveryInformation string|null
     * @param string|NULL $billingName
     * @param string|NULL $billingAddress
     * @param string|NULL $billingCity
     * @param string|NULL $billingPostalCode
     * @param string|NULL $billingCountry
     * @param string|NULL $billingIn
     * @param string|NULL $billingVatId
     * @param string|NULL $billingTelephone
     * @param string|NULL $billingEmail
     * @param string|NULL $billingBankAccount
     * @param string|NULL $comment
     * @return ShoppingCart
     * @throws ShoppingCartSaveFacadeException
     */
    public function update(int $cartId,
                           string $name = NULL,
                           string $firstName = NULL,
                           string $lastName = NULL,
                           string $email = NULL,
                           string $telephone = NULL,
                           string $deliveryFirstName = NULL,
                           string $deliveryLastName = NULL,
                           string $deliveryCompany = NULL,
                           string $deliveryAddress = NULL,
                           string $deliveryCity = NULL,
                           string $deliveryPostalCode = NULL,
                           string $deliveryCountry = NULL,
                           string $deliveryInformation = NULL,
                           string $billingName = NULL,
                           string $billingAddress = NULL,
                           string $billingCity = NULL,
                           string $billingPostalCode = NULL,
                           string $billingCountry = NULL,
                           string $billingIn = NULL,
                           string $billingVatId = NULL,
                           string $billingTelephone = NULL,
                           string $billingEmail = NULL,
                           string $billingBankAccount = NULL,
                           string $comment = NULL
    )
    {
        try {
            $cartRepo = $this->shoppingCartRepoFactory->create();
            $cart = $cartRepo->getOneById($cartId, $this->translator);

            $cart->setName($name);
            $cart->setFirstName($firstName);
            $cart->setLastName($lastName);
            $cart->setEmail($email);
            $cart->setTelephone($telephone);
            $cart->setDeliveryFirstName($deliveryFirstName);
            $cart->setDeliveryLastName($deliveryLastName);
            $cart->setDeliveryCompany($deliveryCompany);
            $cart->setDeliveryAddress($deliveryAddress);
            $cart->setDeliveryCity($deliveryCity);
            $cart->setDeliveryPostalCode($deliveryPostalCode);
            $cart->setDeliveryCountry($deliveryCountry);
            $cart->setDeliveryInformation($deliveryInformation);
            $cart->setBillingName($billingName);
            $cart->setBillingAddress($billingAddress);
            $cart->setBillingCity($billingCity);
            $cart->setBillingPostalCode($billingPostalCode);
            $cart->setBillingCountry($billingCountry);
            $cart->setBillingIn($billingIn);
            $cart->setBillingVatId($billingVatId);
            $cart->setBillingTelephone($billingTelephone);
            $cart->setBillingEmail($billingEmail);
            $cart->setBillingBankAccount($billingBankAccount);
            $cart->setComment($comment);

            $cartRepo = $this->shoppingCartRepoFactory->create();
            $cartRepo->save($cart);

            return $cart;
        } catch (ShoppingCartNotFoundException $exception) {
            throw new ShoppingCartSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * Apply birthday coupon on cart.
     * @param $cartId int
     * @return ShoppingCart
     * @throws ShoppingCartSaveFacadeException
     * todo test
     */
    public function applyBirthdayCoupon(int $cartId) : ShoppingCart
    {
        try {
            $cartRepo = $this->shoppingCartRepoFactory->create();
            $cart = $cartRepo->getOneById($cartId, $this->translator);
            if ($cart->getCustomerId() === NULL) {
                throw new ShoppingCartSaveFacadeException($this->translator->translate(sprintf('%s.birthdaycoupon.missingcustomer', ShoppingCartTranslation::getFileName())));
            }
            $customerRepo = $this->customerRepositoryFactory->create();
            $customer = $customerRepo->getOneAllowedById((int)$cart->getCustomerId());
            if ($customer->hasBirthdayCoupon() === TRUE) {
                $this->birthdayCoupon->apply($cart);
                return $cart;
            }
            throw new ShoppingCartSaveFacadeException($this->translator->translate('customer.birthdayCoupon.canNotBeApplied'));
        } catch (ShoppingCartNotFoundException $exception) {
            throw new ShoppingCartSaveFacadeException($exception->getMessage());
        } catch (CustomerNotFoundException $exception) {
            throw new ShoppingCartSaveFacadeException($this->translator->translate('customer.not.found'));
        }
    }



    /**
     * Remove birthday coupon from shopping cart.
     * @param $cartId int
     * @return ShoppingCart
     * @throws ShoppingCartSaveFacadeException
     * todo test
     */
    public function removeBirthdayCoupon(int $cartId) : ShoppingCart
    {
        try {
            $cartRepo = $this->shoppingCartRepoFactory->create();
            $cart = $cartRepo->getOneById($cartId, $this->translator);
            $this->birthdayCoupon->remove($cart);
            return $cart;
        } catch (ShoppingCartNotFoundException $exception) {
            throw new ShoppingCartSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * Set customer on cart.
     * @param $cartId int
     * @param $customerId int
     * @return string
     * @throws ShoppingCartSaveFacadeException
     * todo test
     */
    public function setCustomerOnCart(int $cartId, int $customerId) : string
    {
        $cartRepo = $this->shoppingCartRepoFactory->create();
        $customerRepo = $this->customerRepositoryFactory->create();

        try {
            $customer = $customerRepo->getOneAllowedById($customerId);
            $cart = $cartRepo->getOneById($cartId, $this->translator);
            $cartProducts = $this->cartProductRepo->findByCartId($cart->getId());

            try {
                $cart->setFirstName($customer->getFirstName());
                $cart->setLastName($customer->getLastName());
                $cart->setBillingEmail($customer->getEmail());
                $cart->setBillingTelephone($customer->getTelephone());
                $cart->setBillingAddress($customer->getStreet());
                $cart->setBillingCity($customer->getCity());
                $cart->setBillingPostalCode($customer->getPostcode());
                $cart->setBillingCountry($customer->getCountryCode());
								$cart->setBirthdayCoupon($customer->getBirthdayCoupon());
								
                $returnState = self::MERGED;
                $customerCart = $cartRepo->getOneByCustomerId($customer->getId());
                if ($cartProducts) {
                    $customerCartProducts = $this->cartProductRepo->findByCartId($customerCart->getId()) ?: [];
                    if ($customerCartProducts) {
                        $customerCartProducts = Entities::setValueAsKey($customerCartProducts, 'productId');
                        $returnState = self::MERGED_PRODUCTS;
                    }
                    $products = $this->productMerger->toCustomerCartProducts($customerCart, $cartProducts, $customerCartProducts);
                    $this->cartProductRepo->save($products);
                }
                $cartRepo->remove($cart);
                $this->session->getSection(ShoppingCart::SESSION_SECTION)->remove();
                return $returnState;
            } catch (ShoppingCartNotFoundException $exception) {
                //nothing..
            }

            $cart->setCustomerId($customer->getId());
            $cartRepo->save($cart);

            //apply discount on products
            foreach ($cartProducts as $cartProduct) {
                $this->discount->applyDiscount($cart, $cartProduct);
                $this->cartProductRepo->save($cartProduct);
            }
            $this->session->getSection(ShoppingCart::SESSION_SECTION)->remove();

            return self::CHANGED;
        } catch (CustomerNotFoundException $exception) {
            throw new ShoppingCartSaveFacadeException($this->translator->translate('customer.not.found'));
        } catch (ShoppingCartNotFoundException $exception) {
            throw new ShoppingCartSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * Remove customer from shopping cart.
     * @param $shoppingCart int
     * @return ShoppingCart
     * @throws ShoppingCartSaveFacadeException
     */
    public function removeCustomer(int $shoppingCart) : ShoppingCart
    {
        $cartRepo = $this->shoppingCartRepoFactory->create();

        try {
            $cart = $cartRepo->getOneById($shoppingCart, $this->translator);
            $cart->setCustomerId(NULL);
            $cartRepo->save($cart);
            $products = $this->cartProductRepo->findByCartId($cart->getId());
            foreach ($products as $product) {
                $product->removeDiscount();
                $this->cartProductRepo->save($product);
            }
            $this->session->getSection(ShoppingCart::SESSION_SECTION)->id = $cart->getId();

            return $cart;
        } catch (ShoppingCartNotFoundException $exception) {
            throw new ShoppingCartSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * @param ShoppingCart $shoppingCart
     * @return ShoppingCart
     */
    protected function setIpAddress(ShoppingCart $shoppingCart) : ShoppingCart
    {
        $ipAddress = new ShoppingCartIpAddress();
        $ipAddress->setIpAddress($shoppingCart);

        return $shoppingCart;
    }



    /**
     * @param ShoppingCart $shoppingCart
     * @return ShoppingCart
     */
    protected function setHash(ShoppingCart $shoppingCart) : ShoppingCart
    {
        $hash = new ShoppingCartHash();
        $hash->setHash($shoppingCart, $hash::generateHash());

        return $shoppingCart;
    }
		
		
    /**
     * @param ShoppingCart $shoppingCart
     * @return ShoppingCart
     */
    protected function setBirthdayDiscount(ShoppingCart $shoppingCart, bool $isBirthdayCoupon) : ShoppingCart
    {
				$shoppingCart->setBirthdayCoupon($isBirthdayCoupon);
        return $shoppingCart;
    }
		
}