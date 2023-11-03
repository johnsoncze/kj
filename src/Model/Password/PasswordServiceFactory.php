<?php

namespace App\Password;

interface PasswordServiceFactory
{


    /**
     * @return \App\Password\PasswordService
     */
    public function create();
}