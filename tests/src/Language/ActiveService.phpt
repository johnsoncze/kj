<?php

namespace App\Tests\Language;

use App\Language\LanguageActiveService;
use App\Language\LanguageActiveServiceFactory;
use App\Language\LanguageEntity;
use App\ServiceException;
use App\Tests\BaseTestCase;
use Tester\Assert;

require_once __DIR__ . "/../bootstrap.php";

class ActiveService extends BaseTestCase
{


    public function testSetActive()
    {
        $entity = new LanguageEntity();
        $service = $this->container->getByType(LanguageActiveServiceFactory::class)->create();
        $service->setActive($entity);
        Assert::true($entity->getActive());
    }



    public function testSetDeactive()
    {
        $entity = new LanguageEntity();
        $service = $this->container->getByType(LanguageActiveServiceFactory::class)->create();
        $service->setDeactive($entity);
        Assert::false($entity->getActive());
    }



    public function testDeactiveDefaultLanguage()
    {
        $entity = new LanguageEntity();
        $entity->setDefault(TRUE);
        $service = $this->container->getByType(LanguageActiveServiceFactory::class)->create();
        Assert::exception(function () use ($service, $entity) {
            $service->setDeactive($entity);
        }, ServiceException::class);
    }
}

(new ActiveService())->run();