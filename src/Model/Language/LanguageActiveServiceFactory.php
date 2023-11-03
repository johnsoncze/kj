<?php

namespace App\Language;

interface LanguageActiveServiceFactory
{


    /**
     * @return LanguageActiveService
     */
    public function create();
}