<?php

namespace App\Language;

interface LanguageFacadeFactory
{


    /**
     * @return LanguageFacade
     */
    public function create();
}