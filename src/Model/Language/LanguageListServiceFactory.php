<?php

namespace App\Language;

interface LanguageListServiceFactory
{


    /**
     * @return LanguageListService
     */
    public function create();
}