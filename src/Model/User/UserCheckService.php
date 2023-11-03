<?php

namespace App\User;

use App\ServiceException;
use App\NObject;


class UserCheckService extends NObject
{


    /**
     * @param $userEntity UserEntity|null
     * @return void
     * @throws ServiceException
     */
    public function checkDuplicate(UserEntity $userEntity = null)
    {
        if ($userEntity) {
            throw new ServiceException("Uživatel s e-mailovou adresou '{$userEntity->getEmail()}' již existuje.");
        }
    }
}