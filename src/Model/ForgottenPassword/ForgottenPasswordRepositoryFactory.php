<?php

namespace App\ForgottenPassword;

interface ForgottenPasswordRepositoryFactory
{


    /**
     * @return ForgottenPasswordRepository
     */
    public function create();
}