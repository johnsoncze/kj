<?php

namespace App;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait PublicationTrait
{


    use StatusTrait;

    /**
     * @deprecated use $states
     */
    protected static $statuses = [
        self::PUBLISH => [
            "key" => self::PUBLISH,
            "translate" => "Zobrazovat"
        ], self::DRAFT => [
            "key" => self::DRAFT,
            "translate" => "Nezobrazovat"
        ]
    ];

    /** @var array */
    protected static $states = [
        self::PUBLISH => [
            "key" => self::PUBLISH,
            "translation" => "Zobrazovat"
        ], self::DRAFT => [
            "key" => self::DRAFT,
            "translation" => "Nezobrazovat"
        ]
    ];
}