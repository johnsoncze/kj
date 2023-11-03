<?php

namespace App\Language;

interface LanguageRepositoryFactory
{


    /**
     * @return LanguageRepository
     */
    public function create();
}