<?php

namespace App\Password;

use App\BaseService;
use App\Customer\Customer;


class PasswordService extends BaseService
{


    /**
     * Verify password
     * @param $customer Customer
     * @param $password mixed
     * @return void
     * @throws PasswordServiceException
     */
    public function verify(Customer $customer, $password)
    {
        if ($customer->getPassword() !== $this->createHash($customer, $password)) {
            throw new PasswordServiceException($this->translator->translate(self::class . ".verifyFail"));
        }
    }



    /**
     * Hash password.
     * @param $customer Customer
     * @param $password string
     * @return string
     */
    public function hash(Customer $customer, string $password) : string
    {
        return $this->createHash($customer, $password);
    }



    /**
     * @param $customer Customer
     * @param $password mixed
     * @return string
     */
    protected function createHash(Customer $customer, $password) : string
    {
        $salt = 'JKs467dER9z5In5';
        $passwordId = $customer->getPasswordSuffixId() ?: NULL;
        return sha1($salt . sha1($password) . $passwordId);
    }
}

class PasswordServiceException extends \Exception
{


}