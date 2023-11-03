<?php

namespace App\ForgottenPassword;

use App\Customer\Customer;
use App\User\UserEntity;


class ForgottenPasswordEntityFactory
{


    /**
     * @param $userEntity UserEntity
     * @return ForgottenPasswordEntity
     */
    public function createFromUser(UserEntity $userEntity)
    {
        $entity = new ForgottenPasswordEntity();
        $entity->setUserId($userEntity->getId());
        $entity->setAddDate(new \DateTime());
        return $entity;
    }



    /**
     * @param $customer Customer
     * @return ForgottenPasswordEntity
     */
    public function createFromCustomer(Customer $customer)
    {
        $entity = new ForgottenPasswordEntity();
        $entity->setCustomerId($customer->getId());
        $entity->setAddDate(new \DateTime());
        return $entity;
    }
}