<?php

namespace App\Components\LanguageMiniSwitcher;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface LanguageMiniSwitcherFactory
{


    /**
     * @return LanguageMiniSwitcher
     */
    public function create();
}