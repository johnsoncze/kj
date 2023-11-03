<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart;

use App\Customer\Customer;
use App\Customer\CustomerRepository;
use App\Customer\CustomerRepositoryFactory;
use App\Customer\CustomerTranslation;
use App\ShoppingCart\ShoppingCart;
use App\ShoppingCart\ShoppingCartIpAddress;
use App\ShoppingCart\ShoppingCartRepository;
use App\ShoppingCart\ShoppingCartRepositoryFactory;
use App\ShoppingCart\ShoppingCartSaveFacadeException;
use App\ShoppingCart\ShoppingCartSaveFacadeFactory;
use App\Tests\BaseTestCase;
use App\Tests\Customer\CustomerTestTrait;
use Kdyby\Translation\ITranslator;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ShoppingCartSaveFacadeTest extends BaseTestCase
{

    use CustomerTestTrait;

    /** @var ShoppingCartRepository|null */
    protected $shoppingCartRepo;

    /** @var CustomerRepository|null */
    protected $customerRepo;

    /** @var ITranslator */
    protected $translator;

    /** @var ShoppingCart|null */
    protected $shoppingCart;

    /** @var Customer|null */
    protected $customer;



    protected function setUp()
    {
        parent::setUp();

        $shoppingCartRepoFactory = $this->container->getByType(ShoppingCartRepositoryFactory::class);
        $this->shoppingCartRepo = $shoppingCartRepoFactory->create();

        $this->translator = $this->container->getByType(ITranslator::class);

        $customerRepoFactory = $this->container->getByType(CustomerRepositoryFactory::class);
        $this->customerRepo = $customerRepoFactory->create();
    }



    public function testSaveNew()
    {
        /** @var $saveFacadeFactory ShoppingCartSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $this->shoppingCart = $shoppingCart = $saveFacade->saveNew();

        //load the shopping cart from storage
        $shoppingCartFromStorage = $this->shoppingCartRepo->getOneById((int)$shoppingCart->getId(), $this->translator);

        Assert::type(ShoppingCart::class, $shoppingCart);
        Assert::same(ShoppingCartIpAddress::CONSOLE, $shoppingCart->getIpAddress());
        Assert::null($shoppingCart->getCustomerId());
        Assert::null($shoppingCart->getName());
        Assert::null($shoppingCart->getEmail());
        Assert::null($shoppingCart->getTelephone());
        Assert::null($shoppingCart->getDeliveryAddress());
        Assert::null($shoppingCart->getDeliveryCity());
        Assert::null($shoppingCart->getDeliveryPostalCode());
        Assert::null($shoppingCart->getDeliveryCountry());
        Assert::null($shoppingCart->getBillingName());
        Assert::null($shoppingCart->getBillingAddress());
        Assert::null($shoppingCart->getBillingCity());
        Assert::null($shoppingCart->getBillingPostalCode());
        Assert::null($shoppingCart->getBillingCountry());
        Assert::null($shoppingCart->getBillingIn());
        Assert::null($shoppingCart->getBillingVatId());
        Assert::null($shoppingCart->getBillingTelephone());
        Assert::null($shoppingCart->getBillingEmail());
        Assert::null($shoppingCart->getBillingBankAccount());
        Assert::null($shoppingCart->getComment());
        Assert::false($shoppingCart->getBirthdayCoupon());
        Assert::truthy($shoppingCart->getHash());

        Assert::type(ShoppingCart::class, $shoppingCartFromStorage);
        Assert::same(ShoppingCartIpAddress::CONSOLE, $shoppingCartFromStorage->getIpAddress());
        Assert::null($shoppingCartFromStorage->getCustomerId());
        Assert::null($shoppingCartFromStorage->getName());
        Assert::null($shoppingCartFromStorage->getEmail());
        Assert::null($shoppingCartFromStorage->getTelephone());
        Assert::null($shoppingCartFromStorage->getDeliveryAddress());
        Assert::null($shoppingCartFromStorage->getDeliveryCity());
        Assert::null($shoppingCartFromStorage->getDeliveryPostalCode());
        Assert::null($shoppingCartFromStorage->getDeliveryCountry());
        Assert::null($shoppingCartFromStorage->getBillingName());
        Assert::null($shoppingCartFromStorage->getBillingAddress());
        Assert::null($shoppingCartFromStorage->getBillingCity());
        Assert::null($shoppingCartFromStorage->getBillingPostalCode());
        Assert::null($shoppingCartFromStorage->getBillingCountry());
        Assert::null($shoppingCartFromStorage->getBillingIn());
        Assert::null($shoppingCartFromStorage->getBillingVatId());
        Assert::null($shoppingCartFromStorage->getBillingTelephone());
        Assert::null($shoppingCartFromStorage->getBillingEmail());
        Assert::null($shoppingCartFromStorage->getBillingBankAccount());
        Assert::null($shoppingCartFromStorage->getComment());
        Assert::false($shoppingCartFromStorage->getBirthdayCoupon());
        Assert::truthy($shoppingCartFromStorage->getHash());
    }



    public function testSaveNewForAllowedCustomer()
    {
        //save a test customer
        $customer = $this->createTestCustomer();
        $this->customer = $this->customerRepo->save($customer);

        /** @var $saveFacadeFactory ShoppingCartSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $this->shoppingCart = $shoppingCart = $saveFacade->saveNew((int)$customer->getId());

        //load the shopping cart from storage
        $shoppingCartFromStorage = $this->shoppingCartRepo->getOneById((int)$shoppingCart->getId(), $this->translator);

        Assert::type(ShoppingCart::class, $shoppingCart);
        Assert::same(ShoppingCartIpAddress::CONSOLE, $shoppingCart->getIpAddress());
        Assert::same((int)$customer->getId(), (int)$shoppingCart->getCustomerId());
        Assert::null($shoppingCart->getName());
        Assert::null($shoppingCart->getEmail());
        Assert::null($shoppingCart->getTelephone());
        Assert::null($shoppingCart->getDeliveryAddress());
        Assert::null($shoppingCart->getDeliveryCity());
        Assert::null($shoppingCart->getDeliveryPostalCode());
        Assert::null($shoppingCart->getDeliveryCountry());
        Assert::null($shoppingCart->getBillingName());
        Assert::null($shoppingCart->getBillingAddress());
        Assert::null($shoppingCart->getBillingCity());
        Assert::null($shoppingCart->getBillingPostalCode());
        Assert::null($shoppingCart->getBillingCountry());
        Assert::null($shoppingCart->getBillingIn());
        Assert::null($shoppingCart->getBillingVatId());
        Assert::null($shoppingCart->getBillingTelephone());
        Assert::null($shoppingCart->getBillingEmail());
        Assert::null($shoppingCart->getBillingBankAccount());
        Assert::null($shoppingCart->getComment());
        Assert::false($shoppingCart->getBirthdayCoupon());
        Assert::truthy($shoppingCart->getHash());

        Assert::type(ShoppingCart::class, $shoppingCartFromStorage);
        Assert::same(ShoppingCartIpAddress::CONSOLE, $shoppingCartFromStorage->getIpAddress());
        Assert::same((int)$customer->getId(), (int)$shoppingCartFromStorage->getCustomerId());
        Assert::null($shoppingCartFromStorage->getName());
        Assert::null($shoppingCartFromStorage->getEmail());
        Assert::null($shoppingCartFromStorage->getTelephone());
        Assert::null($shoppingCartFromStorage->getDeliveryAddress());
        Assert::null($shoppingCartFromStorage->getDeliveryCity());
        Assert::null($shoppingCartFromStorage->getDeliveryPostalCode());
        Assert::null($shoppingCartFromStorage->getDeliveryCountry());
        Assert::null($shoppingCartFromStorage->getBillingName());
        Assert::null($shoppingCartFromStorage->getBillingAddress());
        Assert::null($shoppingCartFromStorage->getBillingCity());
        Assert::null($shoppingCartFromStorage->getBillingPostalCode());
        Assert::null($shoppingCartFromStorage->getBillingCountry());
        Assert::null($shoppingCartFromStorage->getBillingIn());
        Assert::null($shoppingCartFromStorage->getBillingVatId());
        Assert::null($shoppingCartFromStorage->getBillingTelephone());
        Assert::null($shoppingCartFromStorage->getBillingEmail());
        Assert::null($shoppingCartFromStorage->getBillingBankAccount());
        Assert::null($shoppingCartFromStorage->getComment());
        Assert::false($shoppingCartFromStorage->getBirthdayCoupon());
        Assert::truthy($shoppingCartFromStorage->getHash());
    }



    public function testSaveNewForForbiddenCustomer()
    {
        //save a test customer
        $customer = $this->createTestCustomer();
        $customer->setState(Customer::FORBIDDEN);
        $this->customer = $this->customerRepo->save($customer);

        /** @var $saveFacadeFactory ShoppingCartSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($customer, $saveFacade) {
            $saveFacade->saveNew((int)$customer->getId());
        }, ShoppingCartSaveFacadeException::class, 'shopping-cart.action.failed');
    }



    public function testSaveNewForUnknownCustomer()
    {
        /** @var $saveFacadeFactory ShoppingCartSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ShoppingCartSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->saveNew(1);
        }, ShoppingCartSaveFacadeException::class, 'shopping-cart.action.failed');
    }



//    public function testUpdate()
//    {
//        $name = 'Dušan Mlynarčík';
//        $email = 'dusan.mlynarcik@email.cz';
//        $telephone = '+420773911088';
//        $deliveryAddress = 'Václavské náměstí 1, Praha 1 - Nové Město';
//        $deliveryCity = 'Praha';
//        $deliveryPostalCode = '110 00';
//        $deliveryCountry = 'Česká republika';
//        $billingName = 'Název firmy s.r.o.';
//        $billingAddress = 'Václavské náměstí 12/157, Praha 1 - Nové Město';
//        $billingCity = 'Praha';
//        $billingPostalCode = '110 00';
//        $billingCountry = 'Česká republika';
//        $billingIn = '01411136';
//        $billingVatId = 'CZ01411136';
//        $billingTelephone = '+420773911088';
//        $billingEmail = 'dusan.mlynarcik@email.cz';
//        $billingBankAccount = '20-2245689978/2547';
//        $comment = 'Přeji si zabalit celou objednávku do dárkového balení.';
//
//        /** @var $saveFacadeFactory ShoppingCartSaveFacadeFactory */
//        $saveFacadeFactory = $this->container->getByType(ShoppingCartSaveFacadeFactory::class);
//        $saveFacade = $saveFacadeFactory->create();
//
//        //save a test shopping cart
//        $this->shoppingCart = $saveFacade->saveNew();
//        $shoppingCartId = (int)$this->shoppingCart->getId();
//
//        //update
//        $shoppingCart = $saveFacade->update($shoppingCartId, $name, $email, $telephone, $deliveryAddress, $deliveryCity, $deliveryPostalCode, $deliveryCountry,
//            $billingName, $billingAddress, $billingCity, $billingPostalCode, $billingCountry, $billingIn, $billingVatId, $billingTelephone,
//            $billingEmail, $billingBankAccount, $comment);
//
//        //load the shopping cart from storage
//        $shoppingCartFromStorage = $this->shoppingCartRepo->getOneById((int)$shoppingCart->getId(), $this->translator);
//
//        Assert::type(ShoppingCart::class, $shoppingCart);
//        Assert::null($shoppingCart->getCustomerId());
//        Assert::same($this->shoppingCart->getIpAddress(), $shoppingCart->getIpAddress());
//        Assert::same($shoppingCartId, (int)$shoppingCart->getId());
//        Assert::same($name, $shoppingCart->getName());
//        Assert::same($email, $shoppingCart->getEmail());
//        Assert::same($telephone, $shoppingCart->getTelephone());
//        Assert::same($deliveryAddress, $shoppingCart->getDeliveryAddress());
//        Assert::same($deliveryCity, $shoppingCart->getDeliveryCity());
//        Assert::same($deliveryCountry, $shoppingCart->getDeliveryCountry());
//        Assert::same($billingName, $shoppingCart->getBillingName());
//        Assert::same($billingAddress, $shoppingCart->getBillingAddress());
//        Assert::same($billingCity, $shoppingCart->getBillingCity());
//        Assert::same($billingPostalCode, $shoppingCart->getBillingPostalCode());
//        Assert::same($billingCountry, $shoppingCart->getBillingCountry());
//        Assert::same($billingIn, $shoppingCart->getBillingIn());
//        Assert::same($billingVatId, $shoppingCart->getBillingVatId());
//        Assert::same($billingTelephone, $shoppingCart->getBillingTelephone());
//        Assert::same($billingEmail, $shoppingCart->getBillingEmail());
//        Assert::same($billingBankAccount, $shoppingCart->getBillingBankAccount());
//        Assert::same($comment, $shoppingCart->getComment());
//        Assert::false($shoppingCart->getBirthdayCoupon());
//        Assert::same($this->shoppingCart->getHash(), $shoppingCart->getHash());
//
//        Assert::type(ShoppingCart::class, $shoppingCartFromStorage);
//        Assert::null($shoppingCartFromStorage->getCustomerId());
//        Assert::same($this->shoppingCart->getIpAddress(), $shoppingCartFromStorage->getIpAddress());
//        Assert::same($shoppingCartId, (int)$shoppingCartFromStorage->getId());
//        Assert::same($name, $shoppingCartFromStorage->getName());
//        Assert::same($email, $shoppingCartFromStorage->getEmail());
//        Assert::same($telephone, $shoppingCartFromStorage->getTelephone());
//        Assert::same($deliveryAddress, $shoppingCartFromStorage->getDeliveryAddress());
//        Assert::same($deliveryCity, $shoppingCartFromStorage->getDeliveryCity());
//        Assert::same($deliveryCountry, $shoppingCartFromStorage->getDeliveryCountry());
//        Assert::same($billingName, $shoppingCartFromStorage->getBillingName());
//        Assert::same($billingAddress, $shoppingCartFromStorage->getBillingAddress());
//        Assert::same($billingCity, $shoppingCartFromStorage->getBillingCity());
//        Assert::same($billingPostalCode, $shoppingCartFromStorage->getBillingPostalCode());
//        Assert::same($billingCountry, $shoppingCartFromStorage->getBillingCountry());
//        Assert::same($billingIn, $shoppingCartFromStorage->getBillingIn());
//        Assert::same($billingVatId, $shoppingCartFromStorage->getBillingVatId());
//        Assert::same($billingTelephone, $shoppingCartFromStorage->getBillingTelephone());
//        Assert::same($billingEmail, $shoppingCartFromStorage->getBillingEmail());
//        Assert::same($billingBankAccount, $shoppingCartFromStorage->getBillingBankAccount());
//        Assert::same($comment, $shoppingCartFromStorage->getComment());
//        Assert::false($shoppingCartFromStorage->getBirthdayCoupon());
//        Assert::same($this->shoppingCart->getHash(), $shoppingCartFromStorage->getHash());
//    }
//
//
//
//    public function testUpdateShoppingCartForCustomer()
//    {
//        //save a test customer
//        $customer = $this->createTestCustomer();
//        $this->customer = $this->customerRepo->save($customer);
//
//        $name = 'Dušan Mlynarčík';
//        $email = 'dusan.mlynarcik@email.cz';
//        $telephone = '+420773911088';
//        $deliveryAddress = 'Václavské náměstí 1, Praha 1 - Nové Město';
//        $deliveryCity = 'Praha';
//        $deliveryPostalCode = '110 00';
//        $deliveryCountry = 'Česká republika';
//        $billingName = 'Název firmy s.r.o.';
//        $billingAddress = 'Václavské náměstí 12/157, Praha 1 - Nové Město';
//        $billingCity = 'Praha';
//        $billingPostalCode = '110 00';
//        $billingCountry = 'Česká republika';
//        $billingIn = '01411136';
//        $billingVatId = 'CZ01411136';
//        $billingTelephone = '+420773911088';
//        $billingEmail = 'dusan.mlynarcik@email.cz';
//        $billingBankAccount = '20-2245689978/2547';
//        $comment = 'Přeji si zabalit celou objednávku do dárkového balení.';
//
//        /** @var $saveFacadeFactory ShoppingCartSaveFacadeFactory */
//        $saveFacadeFactory = $this->container->getByType(ShoppingCartSaveFacadeFactory::class);
//        $saveFacade = $saveFacadeFactory->create();
//
//        //save a test shopping cart
//        $this->shoppingCart = $saveFacade->saveNew((int)$customer->getId());
//        $shoppingCartId = (int)$this->shoppingCart->getId();
//
//        //update
//        $shoppingCart = $saveFacade->update($shoppingCartId, $name, $email, $telephone, $deliveryAddress, $deliveryCity, $deliveryPostalCode, $deliveryCountry,
//            $billingName, $billingAddress, $billingCity, $billingPostalCode, $billingCountry, $billingIn, $billingVatId, $billingTelephone,
//            $billingEmail, $billingBankAccount, $comment);
//
//        //load the shopping cart from storage
//        $shoppingCartFromStorage = $this->shoppingCartRepo->getOneById((int)$shoppingCart->getId(), $this->translator);
//
//        Assert::type(ShoppingCart::class, $shoppingCart);
//        Assert::same((int)$customer->getId(), $shoppingCart->getCustomerId());
//        Assert::same($this->shoppingCart->getIpAddress(), $shoppingCart->getIpAddress());
//        Assert::same($shoppingCartId, (int)$shoppingCart->getId());
//        Assert::same($name, $shoppingCart->getName());
//        Assert::same($email, $shoppingCart->getEmail());
//        Assert::same($telephone, $shoppingCart->getTelephone());
//        Assert::same($deliveryAddress, $shoppingCart->getDeliveryAddress());
//        Assert::same($deliveryCity, $shoppingCart->getDeliveryCity());
//        Assert::same($deliveryCountry, $shoppingCart->getDeliveryCountry());
//        Assert::same($billingName, $shoppingCart->getBillingName());
//        Assert::same($billingAddress, $shoppingCart->getBillingAddress());
//        Assert::same($billingCity, $shoppingCart->getBillingCity());
//        Assert::same($billingPostalCode, $shoppingCart->getBillingPostalCode());
//        Assert::same($billingCountry, $shoppingCart->getBillingCountry());
//        Assert::same($billingIn, $shoppingCart->getBillingIn());
//        Assert::same($billingVatId, $shoppingCart->getBillingVatId());
//        Assert::same($billingTelephone, $shoppingCart->getBillingTelephone());
//        Assert::same($billingEmail, $shoppingCart->getBillingEmail());
//        Assert::same($billingBankAccount, $shoppingCart->getBillingBankAccount());
//        Assert::same($comment, $shoppingCart->getComment());
//        Assert::false($shoppingCart->getBirthdayCoupon());
//        Assert::same($this->shoppingCart->getHash(), $shoppingCart->getHash());
//
//        Assert::type(ShoppingCart::class, $shoppingCartFromStorage);
//        Assert::same((int)$customer->getId(), $shoppingCartFromStorage->getCustomerId());
//        Assert::same($this->shoppingCart->getIpAddress(), $shoppingCartFromStorage->getIpAddress());
//        Assert::same($shoppingCartId, (int)$shoppingCartFromStorage->getId());
//        Assert::same($name, $shoppingCartFromStorage->getName());
//        Assert::same($email, $shoppingCartFromStorage->getEmail());
//        Assert::same($telephone, $shoppingCartFromStorage->getTelephone());
//        Assert::same($deliveryAddress, $shoppingCartFromStorage->getDeliveryAddress());
//        Assert::same($deliveryCity, $shoppingCartFromStorage->getDeliveryCity());
//        Assert::same($deliveryCountry, $shoppingCartFromStorage->getDeliveryCountry());
//        Assert::same($billingName, $shoppingCartFromStorage->getBillingName());
//        Assert::same($billingAddress, $shoppingCartFromStorage->getBillingAddress());
//        Assert::same($billingCity, $shoppingCartFromStorage->getBillingCity());
//        Assert::same($billingPostalCode, $shoppingCartFromStorage->getBillingPostalCode());
//        Assert::same($billingCountry, $shoppingCartFromStorage->getBillingCountry());
//        Assert::same($billingIn, $shoppingCartFromStorage->getBillingIn());
//        Assert::same($billingVatId, $shoppingCartFromStorage->getBillingVatId());
//        Assert::same($billingTelephone, $shoppingCartFromStorage->getBillingTelephone());
//        Assert::same($billingEmail, $shoppingCartFromStorage->getBillingEmail());
//        Assert::same($billingBankAccount, $shoppingCartFromStorage->getBillingBankAccount());
//        Assert::same($comment, $shoppingCartFromStorage->getComment());
//        Assert::false($shoppingCartFromStorage->getBirthdayCoupon());
//        Assert::same($this->shoppingCart->getHash(), $shoppingCartFromStorage->getHash());
//    }
//
//
//
//    public function testUpdateUnknownShoppingCart()
//    {
//        $name = 'Dušan Mlynarčík';
//        $email = 'dusan.mlynarcik@email.cz';
//        $telephone = '+420773911088';
//        $deliveryAddress = 'Václavské náměstí 1, Praha 1 - Nové Město';
//        $deliveryCity = 'Praha';
//        $deliveryPostalCode = '110 00';
//        $deliveryCountry = 'Česká republika';
//        $billingName = 'Název firmy s.r.o.';
//        $billingAddress = 'Václavské náměstí 12/157, Praha 1 - Nové Město';
//        $billingCity = 'Praha';
//        $billingPostalCode = '110 00';
//        $billingCountry = 'Česká republika';
//        $billingIn = '01411136';
//        $billingVatId = 'CZ01411136';
//        $billingTelephone = '+420773911088';
//        $billingEmail = 'dusan.mlynarcik@email.cz';
//        $billingBankAccount = '20-2245689978/2547';
//        $comment = 'Přeji si zabalit celou objednávku do dárkového balení.';
//
//        /** @var $saveFacadeFactory ShoppingCartSaveFacadeFactory */
//        $saveFacadeFactory = $this->container->getByType(ShoppingCartSaveFacadeFactory::class);
//        $saveFacade = $saveFacadeFactory->create();
//
//        Assert::exception(function () use ($saveFacade) {
//            $saveFacade->update(1, 'Dušan Mlynarčík', 'dusan.mlynarcik@email.cz', '+420773911088', 'Václavské náměstí 1, Praha 1 - Nové Město', 'Praha', '110 00', 'Česká republika',
//                'Název firmy s.r.o.', 'Václavské náměstí 12/157, Praha 1 - Nové Město', 'Praha', '110 00', 'Česká republika', '01411136', 'CZ01411136', '+420773911088',
//                'dusan.mlynarcik@email.cz', '20-2245689978/2547', 'Přeji si zabalit celou objednávku do dárkového balení.');
//        }, ShoppingCartSaveFacadeException::class, sprintf('%s.not.found', ShoppingCartTranslation::getFileName()));
//    }



    public function tearDown()
    {
        //delete test data
        if ($this->shoppingCart !== NULL) {
            $this->shoppingCartRepo->remove($this->shoppingCart);
            $this->shoppingCart = NULL;
        }
        if ($this->customer !== NULL) {
            $this->customerRepo->remove($this->customer);
            $this->customer = NULL;
        }
    }


}

(new ShoppingCartSaveFacadeTest())->run();