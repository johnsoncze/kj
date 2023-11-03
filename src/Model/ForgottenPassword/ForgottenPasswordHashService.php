<?php

namespace App\ForgottenPassword;

use App\NObject;
use Nette\Utils\Random;


class ForgottenPasswordHashService extends NObject
{


    /**
     * Set hash
     * @param $entity ForgottenPasswordEntity
     * @return ForgottenPasswordEntity
     */
    public function setHash(ForgottenPasswordEntity $entity)
    {
        $entity->setHash(Random::generate(100));
        return $entity;
    }
}