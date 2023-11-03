<?php

namespace App\Components\UserList;

interface UserListFactory
{


    /**
     * @return UserList
     */
    public function create();
}