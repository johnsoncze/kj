<?php

namespace App\User;

interface UserIdentityServiceFactory
{


    /**
     * @return \App\User\UserIdentityService
     */
    public function create();
}