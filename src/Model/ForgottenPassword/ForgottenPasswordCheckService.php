<?php

namespace App\ForgottenPassword;

use App\ServiceException;
use App\NObject;


class ForgottenPasswordCheckService extends NObject
{


    /**
     * @param $forgottenPasswordEntity ForgottenPasswordEntity
     * @return ForgottenPasswordEntity
     * @throws ServiceException
     */
    public function checkValidityDate(ForgottenPasswordEntity $forgottenPasswordEntity)
    {
        if ($forgottenPasswordEntity->getValidityDate() < date("Y-m-d H:i:s")) {
            throw new ServiceException("Platnost žádosti vypršela.");
        }
        return $forgottenPasswordEntity;
    }
}