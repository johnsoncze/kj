<?php

namespace App\User;

interface UserPasswordServiceFactory
{


    /**
     * @return UserPasswordService
     */
    public function create();
}