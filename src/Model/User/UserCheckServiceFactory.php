<?php

namespace App\User;

interface UserCheckServiceFactory
{


    /**
     * @return \App\User\UserCheckService
     */
    public function create();
}