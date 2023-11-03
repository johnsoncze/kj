<?php

namespace App\Components\PasswordForm;

interface PasswordFormFactory
{


    /**
     * @return PasswordForm
     */
    public function create();
}