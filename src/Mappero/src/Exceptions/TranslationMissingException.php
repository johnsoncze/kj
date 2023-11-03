<?php

namespace Ricaefeliz\Mappero\Exceptions;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TranslationMissingException extends \Exception
{


    const ENTITY_ID = 1;
    const ENTITY_NAME = 2;
    const LANGUAGE = 3;
    const PROPERTY = 4;



    public function __construct(array $message, $code = 0, \Exception $previous = NULL)
    {
        $message = "Missing translation in entity '{$message[self::ENTITY_NAME]}' (ID: '{$message[self::ENTITY_ID]}') for '{$message[self::LANGUAGE]}' language.";
        parent::__construct($message, $code, $previous);
    }

}