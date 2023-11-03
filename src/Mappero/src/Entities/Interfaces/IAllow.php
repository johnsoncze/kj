<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Entities\Interfaces;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IAllow
{


    /** @var string */
    const ALLOWED = 'allowed';

    /** @var string */
    const FORBIDDEN = 'forbidden';
}