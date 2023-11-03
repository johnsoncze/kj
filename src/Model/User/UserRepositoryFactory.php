<?php

namespace App\User;

interface UserRepositoryFactory
{


    /**
     * @return \App\User\UserRepository
     */
    public function create();
}