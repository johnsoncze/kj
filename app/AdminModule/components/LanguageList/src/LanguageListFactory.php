<?php

namespace App\Components\LanguageList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface LanguageListFactory
{


    /**
     * @return LanguageList
     */
    public function create();
}