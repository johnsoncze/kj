<?php

namespace App\Tests\User;

use App\Password\PasswordServiceException;
use App\Password\PasswordServiceFactory;
use App\Tests\BaseTestCase;
use App\User\UserEntity;
use App\User\UserPasswordServiceFactory;
use Tester\Assert;


require __DIR__ . "/../bootstrap.php";

class Password extends BaseTestCase
{


    public function testSetPasswordSuccess()
    {
        $password = "Kočka123!!";
        $user = new UserEntity();
        $userPasswordService = $this->container
            ->getByType(UserPasswordServiceFactory::class)
            ->create();
        $userPasswordService->setPassword($user, $password);
        $passwordService = $this->container
            ->getByType(PasswordServiceFactory::class)
            ->create();
        Assert::noError(function () use ($passwordService, $user, $password) {
            return $passwordService->verify($password, $user->getPassword());
        });
    }



    public function testSetPasswordFail()
    {
        $password = "Kočka123!!";
        $user = new UserEntity();
        $userPasswordService = $this->container
            ->getByType(UserPasswordServiceFactory::class)
            ->create();
        $userPasswordService->setPassword($user, $password);
        $passwordService = $this->container
            ->getByType(PasswordServiceFactory::class)
            ->create();
        Assert::exception(function () use ($passwordService, $user) {
            return $passwordService->verify("JinéHeslo444!", $user->getPassword());
        }, PasswordServiceException::class);
    }
}

(new Password())->run();