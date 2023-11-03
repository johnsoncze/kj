<?php

namespace App\ForgottenPassword;

interface ForgottenPasswordHashServiceFactory
{


    /**
     * @return ForgottenPasswordHashService
     */
    public function create();
}