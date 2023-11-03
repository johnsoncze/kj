<?php

declare(strict_types = 1);

namespace App\Location;

use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class State
{


    /** @var string */
    const CZ = 'CZ';
    const SK = 'SK';

    /** @var array */
    protected static $states = [
        self::CZ => 'location.state.czechRepublic',
        self::SK => 'location.state.slovakia',
    ];



    /**
     * @param $translator ITranslator
     * @return array
     */
    public static function getList(ITranslator $translator) : array
    {
        $list = [];
        $states = self::$states;
        foreach ($states as $key => $translationKey) {
            $list[$key] = $translator->translate($translationKey);
        }
        return $list;
    }



    /**
     * @param $state string
     * @return string
     */
    public static function getTranslationKey(string $state) : string
    {
        return self::$states[$state] ?? $state;
    }
}