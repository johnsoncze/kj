<?php

namespace App\Components\ChooseLanguageForm;

interface ChooseLanguageFormFactory
{


    /**
     * @return ChooseLanguageForm
     */
    public function create();
}