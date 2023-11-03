<?php

namespace App\Tests\User;


use App\Tests\BaseTestCase;
use App\User\UserCheckServiceFactory;
use App\User\UserEntity;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

class UserCheckService extends BaseTestCase
{


    public function testCheckDuplicateTrue()
    {
        $userEntity = new UserEntity();
        $userEntity->setEmail("dusan.mlynarcik@email.cz");
        $userCheckService = $this->container->getByType(UserCheckServiceFactory::class)
            ->create();
        Assert::exception(function () use ($userEntity, $userCheckService) {
            $userCheckService->checkDuplicate($userEntity);
        }, "App\\ServiceException");
    }



    public function testCheckDuplicateFalse()
    {
        $userCheckService = $this->container->getByType(UserCheckServiceFactory::class)
            ->create();
        Assert::noError(function () use ($userCheckService) {
            $userCheckService->checkDuplicate(null);
        });
    }

}

(new UserCheckService())->run();