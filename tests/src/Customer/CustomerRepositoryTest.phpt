<?php

declare(strict_types = 1);

namespace App\Tests\Customer;

use App\Customer\CustomerRepository;
use App\Customer\CustomerRepositoryFactory;
use App\Tests\BaseTestCase;
use Nette\Utils\Random;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CustomerRepositoryTest extends BaseTestCase
{


    use CustomerTestTrait;

    /** @var CustomerRepository */
    private $customerRepo;



    protected function setUp()
    {
        parent::setUp();
        $customerRepoFactory = $this->container->getByType(CustomerRepositoryFactory::class);
        $this->customerRepo = $customerRepoFactory->create();
    }



    public function testFindEmailsForActivation()
    {
        $customer1 = $this->createTestCustomer();
        $customer1->setExternalSystemId(1);
        $customer1->setEmail('dusan1@jk.cz');
        $customer1->setPassword(NULL);
        $customer2 = $this->createTestCustomer();
        $customer2->setExternalSystemId(2);
        $customer2->setEmail('dusan2@jk.cz');
        $customer2->setPassword('password');
        $customer3 = $this->createTestCustomer();
        $customer3->setExternalSystemId(3);
        $customer3->setEmail('dusan3@jk.cz');
        $customer3->setPassword(NULL);
        $customer4 = $this->createTestCustomer();
        $customer4->setExternalSystemId(4);
        $customer4->setEmail('dusan4@jk.cz');
        $customer4->setPassword(NULL);
        $customer4->setActivationToken(Random::generate(32));
        foreach ([$customer1, $customer2, $customer3, $customer4] as $customer) {
            $this->customerRepo->save($customer);
            $this->addEntityForRemove($customer, $this->customerRepo);
        }

        $response = $this->customerRepo->findEmailsForActivation();

        Assert::count(2, $response);
        Assert::same([$customer1->getEmail(), $customer3->getEmail()], $response);
    }
}

(new CustomerRepositoryTest())->run();