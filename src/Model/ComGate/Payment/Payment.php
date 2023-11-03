<?php

declare(strict_types = 1);

namespace App\ComGate\Payment;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Payment
{


    /** @var string */
    protected $id;

    /** @var string */
    protected $redirect;



    public function __construct(string $id, string $redirect)
    {
        $this->id = $id;
        $this->redirect = $redirect;
    }



    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }



    /**
     * @return string
     */
    public function getRedirect(): string
    {
        return $this->redirect;
    }



    /**
     * @param $response array
     * @return self
     * @throws \InvalidArgumentException
     */
    public static function createFromApiResponse(array $response) : self
    {
        $id = $response['transId'] ?? NULL;
        $redirect = $response['redirect'] ?? NULL;
        if ($id === NULL) {
            throw new \InvalidArgumentException('Missing id.');
        }
        if ($redirect === NULL) {
            throw new \InvalidArgumentException('Missing redirect.');
        }
        return new static($id, $redirect);
    }
}