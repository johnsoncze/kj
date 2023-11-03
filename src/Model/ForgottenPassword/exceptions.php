<?php

declare(strict_types = 1);

namespace App\ForgottenPassword;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ForgottenPasswordFacadeException extends \Exception
{


    /** @var int codes */
    const NOT_ACTIVATED = 100;
}