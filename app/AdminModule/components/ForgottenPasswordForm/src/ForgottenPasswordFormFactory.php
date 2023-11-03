<?php

namespace App\Components\ForgottenPasswordForm;

interface ForgottenPasswordFormFactory
{


    /**
     * @return ForgottenPasswordForm
     */
    public function create();
}