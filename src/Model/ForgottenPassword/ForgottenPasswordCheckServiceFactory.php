<?php

namespace App\ForgottenPassword;

interface ForgottenPasswordCheckServiceFactory
{


    /**
     * @return \App\ForgottenPassword\ForgottenPasswordCheckService
     */
    public function create();
}