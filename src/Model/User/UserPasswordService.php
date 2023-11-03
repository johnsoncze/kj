<?php

namespace App\User;

use App\NObject;
use Nette\Security\Passwords;


class UserPasswordService extends NObject
{


    /**
     * Set password
     * @param $userEntity UserEntity
     * @param $password string
     * @return UserEntity
     */
    public function setPassword(UserEntity $userEntity, $password)
    {
        $userEntity->setPassword(Passwords::hash($password));
        return $userEntity;
    }
}