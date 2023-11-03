<?php

namespace App\Language;

interface LanguageListFacadeFactory
{


    /**
     * @return LanguageListFacade
     */
    public function create();
}