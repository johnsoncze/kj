<?php

namespace App\User;

use App\Extensions\Nette\UserIdentity;
use App\NObject;
use Nette\Security\IIdentity;
use Nette\Security\User;


class UserIdentityService extends NObject
{


    /** @var string */
    const NAMESPACE_USER_STORAGE = "admin";



    /**
     * Save logged user identity
     * @param $userEntity UserEntity
     * @param $user User
     * @return User
     */
    public function saveIdentity(UserEntity $userEntity, User $user)
    {
        $identity = new UserIdentity();
        $identity->setId($userEntity->getId());
        $identity->setEntity($userEntity);
        $user->getStorage()->setNamespace(self::NAMESPACE_USER_STORAGE);
        $user->login($identity);
        return $user;
    }



    /**
     * Remove identity
     * @param $user User
     * @return User
     */
    public function removeIdentity(User $user)
    {
        $user->getStorage()->setNamespace(self::NAMESPACE_USER_STORAGE);
        $user->logout(true);
        return $user;
    }



    /**
     * Get identity
     * @param $user User
     * @return IIdentity|null
     */
    public function getIdentity(User $user)
    {
        $user->getStorage()->setNamespace(self::NAMESPACE_USER_STORAGE);
        return $user->getIdentity();
    }

}