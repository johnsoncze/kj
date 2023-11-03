<?php

declare(strict_types = 1);

namespace App\Customer\Activation;

class ActivationFacadeException extends \Exception
{


    /** @var int codes */
    const EXPIRED = 100;

}

class ActivationRequestException extends \Exception
{


}