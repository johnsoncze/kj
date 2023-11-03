<?php

namespace App\ForgottenPassword;

interface ForgottenPasswordFacadeFactory
{


    /**
     * @return \App\ForgottenPassword\ForgottenPasswordFacade
     */
    public function create();
}