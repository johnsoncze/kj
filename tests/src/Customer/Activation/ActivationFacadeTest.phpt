<?php

declare(strict_types = 1);

namespace App\Tests\Customer\Activation;

use App\Customer\Activation\ActivationFacade;
use App\Customer\Activation\ActivationFacadeException;
use App\Customer\Activation\ActivationFacadeFactory;
use App\Customer\Activation\ActivationRequest;
use App\Customer\Customer;
use App\Customer\CustomerRepository;
use App\Facades\MailerFacade;
use App\Tests\BaseTestCase;
use App\Tests\Customer\CustomerTestTrait;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ActivationFacadeTest extends BaseTestCase
{


    use CustomerTestTrait;


    /** @var ActivationFacade */
    private $activationFacade;

    /** @var ActivationRequest */
    private $activationRequest;

    /** @var CustomerRepository */
    private $customerRepo;

    /** @var MailerFacade */
    private $mailerFacade;



    protected function setUp()
    {
        parent::setUp();
        $activationFacadeFactory = $this->container->getByType(ActivationFacadeFactory::class);
        $this->activationFacade = $activationFacadeFactory->create();
        $this->customerRepo = $this->container->getByType(CustomerRepository::class);
        $this->activationRequest = $this->container->getByType(ActivationRequest::class);
        $this->mailerFacade = $this->container->getByType(MailerFacade::class);
        MailerFacade::setTestEnvironment(TRUE);
    }



    public function testActivationSuccess()
    {
        $customer = $this->createTestCustomer();
        $customer->setPassword(NULL);
        $this->activationRequest->setNew($customer);
        $this->saveCustomer($customer);

        $password = 'testpassword1';
        $startDate = new DateTime();

        $response = $this->activationFacade->activate($customer->getEmail(), $customer->getActivationToken(), $password);
        $customerFromStorage = $this->customerRepo->getOneAllowedById($customer->getId());

        Assert::type(Customer::class, $response);
        Assert::type(Customer::class, $customerFromStorage);

        /** @var $customerObject Customer */
        foreach ([$response, $customerFromStorage] as $customerObject) {
            Assert::same($customer->getExternalSystemId(), $customerObject->getExternalSystemId());
            Assert::same($customer->getFirstName(), $customerObject->getFirstName());
            Assert::same($customer->getLastName(), $customerObject->getLastName());
            Assert::same($customer->getSex(), $customerObject->getSex());
            Assert::same($customer->getAddressing(), $customerObject->getAddressing());
            Assert::same($customer->getEmail(), $customerObject->getEmail());
            Assert::same($customer->getTelephone(), $customerObject->getTelephone());
            Assert::same($customer->getStreet(), $customerObject->getStreet());
            Assert::same($customer->getCity(), $customerObject->getCity());
            Assert::same($customer->getPostcode(), $customerObject->getPostcode());
            Assert::same($customer->getCountryCode(), $customerObject->getCountryCode());
            Assert::same($customer->getBirthdayYear(), (int)$customerObject->getBirthdayYear());
            Assert::same((int)$customer->getBirthdayMonth(), (int)$customerObject->getBirthdayMonth());
            Assert::same((int)$customer->getBirthdayDay(), (int)$customerObject->getBirthdayDay());
            Assert::same($customer->getBirthdayCoupon(), $customerObject->getBirthdayCoupon());
            Assert::same($customer->getNewsletter(), $customerObject->getNewsletter());
            Assert::true(Passwords::verify($password, $customerObject->getPassword()));
            Assert::null($customerObject->getActivationToken());
            Assert::null($customerObject->getActivationTokenValidTo());
            Assert::true($customerObject->getActivationDate() >= $startDate->format('Y-m-d H:i:s') && $customerObject->getActivationDate() <= (new DateTime())->format('Y-m-d H:i:s'));
            Assert::same($customer->getExternalSystemLastChangeDate(), $customerObject->getExternalSystemLastChangeDate());
            Assert::same($customer->getState(), $customerObject->getState());
        }
    }



    public function testActivationUnknownCustomer()
    {
        $customer = $this->createTestCustomer();
        $customer->setPassword(NULL);
        $this->activationRequest->setNew($customer);
        $this->saveCustomer($customer);

        Assert::exception(function () use ($customer) {
            $this->activationFacade->activate($customer->getEmail(), 'unknowntoken', 'testpassword1');
        }, ActivationFacadeException::class, 'customer.not.found');

        Assert::exception(function () use ($customer) {
            $this->activationFacade->activate('unknownemail@email.cz', $customer->getActivationToken(), 'testpassword1');
        }, ActivationFacadeException::class, 'customer.not.found');
    }



    public function testActiveNotAllowedCustomer()
    {
        $customer = $this->createTestCustomer();
        $customer->setPassword(NULL);
        $customer->setState(Customer::FORBIDDEN);
        $this->activationRequest->setNew($customer);
        $this->saveCustomer($customer);

        Assert::exception(function () use ($customer) {
            $this->activationFacade->activate($customer->getEmail(), $customer->getActivationToken(), 'testpassword1');
        }, ActivationFacadeException::class, 'customer.not.found');
    }



    public function testActivateActivatedCustomer()
    {
        $customer = $this->createTestCustomer();
        $customer->setPassword(Passwords::hash('testpassword'));
        $this->saveCustomer($customer);

        Assert::exception(function () use ($customer) {
            $this->activationFacade->activate($customer->getEmail(), 'abc', 'testpassword1');
        }, ActivationFacadeException::class, 'customer.not.found');
    }



    public function testCreateRequestSuccess()
    {
        $activationTokenValidFrom = new DateTime('+3 days');

        $customer = $this->createTestCustomer();
        $customer->setPassword(NULL);
        $customer->setActivationToken(NULL);
        $customer->setActivationTokenValidTo(NULL);
        $this->saveCustomer($customer);

        $response = $this->activationFacade->createRequest($customer->getEmail());
        $customerFromStorage = $this->customerRepo->getOneAllowedByEmail($customer->getEmail());
        $emails = MailerFacade::getEmails();
        $email = end($emails);

        Assert::type(Customer::class, $response);
        Assert::type(Customer::class, $customerFromStorage);

        foreach ([$response, $customerFromStorage] as $customerObject) {
            Assert::true(is_string($customerObject->getActivationToken()) && strlen($customerObject->getActivationToken()) === 32);
            Assert::true($activationTokenValidFrom->format('Y-m-d H:i:s') <= $customerObject->getActivationTokenValidTo());
        }

        Assert::count(1, $emails);
        Assert::same('customer.email.storeRegistration.subject', $email->getSubject());
        Assert::same([$customer->getEmail() => NULL], $email->getHeader('To'));
    }



    public function testCreateRequestOnActivatedCustomer()
    {
        $customer = $this->createTestCustomer();
        $this->saveCustomer($customer);

        Assert::exception(function () use ($customer) {
            $this->activationFacade->createRequest($customer->getEmail());
        }, ActivationFacadeException::class, 'customer.activated.already');
    }



    public function testCreateRequestOnNotAllowedCustomer()
    {
        $customer = $this->createTestCustomer();
        $customer->setPassword(NULL);
        $customer->setState(Customer::FORBIDDEN);
        $this->saveCustomer($customer);

        Assert::exception(function () use ($customer) {
            $this->activationFacade->createRequest($customer->getEmail());
        }, ActivationFacadeException::class, 'customer.not.found');
    }



    public function testCreateRequestOnUnknownCustomer()
    {
        Assert::exception(function () {
            $this->activationFacade->createRequest('johndoe@jk.cz');
        }, ActivationFacadeException::class, 'customer.not.found');
    }



    private function saveCustomer(Customer $customer) : Customer
    {
        $this->customerRepo->save($customer);
        $this->addEntityForRemove($customer, $this->customerRepo);
        return $customer;
    }
}

(new ActivationFacadeTest())->run();