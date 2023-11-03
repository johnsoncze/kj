<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Validity;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Message
{


    /** @var string types */
    const TYPE_ERROR = 'error';
    const TYPE_INFO = 'info';


    /** @var string */
    protected $type;

    /** @var string */
    protected $message;



    public function __construct(string $message, string $type = self::TYPE_ERROR)
    {
        $this->message = $message;
        $this->type = $type;
    }



    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }



    /**
     * @return string
    */
    public function getFlashMessageType() : string
    {
        $type = $this->getType();
        return $type === self::TYPE_ERROR ? 'danger' : $type;
    }



    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }


}