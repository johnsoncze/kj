<?php

namespace App\Components\SignInForm;

interface SignInFormFactory
{


    /**
     * @return SignInForm
     */
    public function create();
}