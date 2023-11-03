<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\StateChangeForm;

use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IStateObject
{


    /**
     * Get translated state list.
     * @param $translator ITranslator
     * @return array
     */
    public static function getTranslatedStateList(ITranslator $translator) : array;



    /**
     * @return string
     */
    public function getState();
}