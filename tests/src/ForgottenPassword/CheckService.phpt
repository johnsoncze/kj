<?php

namespace App\Tests\ForgottenPassword;


use App\ForgottenPassword\ForgottenPasswordCheckServiceFactory;
use App\ForgottenPassword\ForgottenPasswordEntity;
use App\ServiceException;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../bootstrap.php";

class CheckService extends BaseTestCase
{


    public function testValidityDateSuccess()
    {
        $entity = new ForgottenPasswordEntity();
        $checkService = $this->container->getByType(ForgottenPasswordCheckServiceFactory::class)
            ->create();
        Assert::type(ForgottenPasswordEntity::class, $checkService->checkValidityDate($entity));
    }



    public function testValidityDateFail()
    {
        $entity = new ForgottenPasswordEntity();
        $entity->setValidityDate("2001-01-01 10:50:45");
        $checkService = $this->container->getByType(ForgottenPasswordCheckServiceFactory::class)
            ->create();
        Assert::exception(function () use ($entity, $checkService) {
            return $checkService->checkValidityDate($entity);
        }, ServiceException::class);
    }
}

(new CheckService())->run();