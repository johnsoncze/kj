<?php


namespace App\ForgottenPassword;

interface ForgottenPasswordEmailServiceFactory
{


    /**
     * @return ForgottenPasswordEmailService
     */
    public function create();
}