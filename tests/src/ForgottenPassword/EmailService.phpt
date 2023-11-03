<?php

namespace App\Tests\ForgottenPassword;

use App\Facades\MailerFacade;
use App\ForgottenPassword\ForgottenPasswordEmailServiceFactory;
use App\ForgottenPassword\ForgottenPasswordEntity;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

class EmailService extends BaseTestCase
{


    public function testSendNewRequestUserEmail()
    {
        MailerFacade::setTestEnvironment(true);
        $entity = new ForgottenPasswordEntity();
        $entity->setUserId(1);
        $service = $this->container->getByType(ForgottenPasswordEmailServiceFactory::class)->create();
        $service->sendNewRequestUser($entity, "dusan.mlynarcik@email.cz");
        Assert::equal(1, count(MailerFacade::getEmails()));
    }



    public function testMissingEmail()
    {
        $entity = new ForgottenPasswordEntity();
        $entity->setUserId(1);
        $service = $this->container->getByType(ForgottenPasswordEmailServiceFactory::class)->create();
        Assert::exception(function () use ($service, $entity) {
            $service->sendNewRequestUser($entity);
        }, "App\\ServiceException");
    }
}

(new EmailService())->run();