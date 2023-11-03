<?php

declare(strict_types = 1);

namespace App\Tests\Customer;

use App\Customer\Customer;
use Nette\Security\Passwords;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait CustomerTestTrait
{


    /**
     * @return Customer
     */
    public function createTestCustomer() : Customer
    {
        $customer = new Customer();
        $customer->setExternalSystemId(1);
        $customer->setFirstName('John');
        $customer->setLastName('Doe');
        $customer->setSex('m');
        $customer->setAddressing('Dear John');
        $customer->setEmail('johndoe@jk.cz');
        $customer->setTelephone('+420123456789');
        $customer->setStreet('Street 1');
        $customer->setCity('Prague');
        $customer->setPostcode('10000');
        $customer->setCountryCode('cz');
        $customer->setBirthdayYear(2018);
        $customer->setBirthdayMonth(05);
        $customer->setBirthdayDay(15);
        $customer->setBirthdayCoupon(FALSE);
        $customer->setNewsletter(TRUE);
        $customer->setPassword(Passwords::hash('testpassword1'));
        $customer->setExternalSystemLastChangeDate('2018-05-12 12:00:00');
        $customer->setState(Customer::ALLOWED);

        return $customer;
    }
}