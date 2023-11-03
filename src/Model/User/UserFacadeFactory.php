<?php

namespace App\User;

interface UserFacadeFactory
{


    /**
     * @return \App\User\UserFacade
     */
    public function create();
}