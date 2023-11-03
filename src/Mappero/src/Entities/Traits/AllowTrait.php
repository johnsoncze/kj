<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Entities\Traits;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait AllowTrait
{


    /** @var array */
    protected static $states = [
        self::ALLOWED => [
            "key" => self::ALLOWED,
            "translation" => "Povoleno"
        ], self::FORBIDDEN => [
            "key" => self::FORBIDDEN,
            "translation" => "Zakázáno"
        ]
    ];
}