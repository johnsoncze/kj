<?php

declare(strict_types = 1);

namespace App\Helpers;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Validators extends \Nette\Utils\Validators
{


    /**
     * @param $email string
     * @param $exceptionClass string
     * @return string
     * @throws \Exception
     */
    public static function checkEmail(string $email, string $exceptionClass = \InvalidArgumentException::class) : string
    {
        if (Validators::isEmail($email) !== TRUE) {
            throw new $exceptionClass(sprintf('E-mail \'%s\' does not have valid format.', $email));
        }
        return $email;
    }
}