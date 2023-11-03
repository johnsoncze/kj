<?php

declare(strict_types = 1);

namespace App\Libs\FileManager\Responses;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class DeleteFileResponse
{


    const SUCCESS = 0;
    const ERROR = 1;

    /** @var string */
    protected $message;

    /** @var int */
    protected $code;



    public function __construct(string $message, int $code = self::SUCCESS)
    {
        $this->checkValidityOfCode($code);
        $this->message = $message;
        $this->code = $code;
    }



    /**
     * @param int $code
     * @return int
     * @throws \InvalidArgumentException
     */
    private function checkValidityOfCode(int $code) : int
    {
        $codes = [self::SUCCESS, self::ERROR];
        if (!in_array($code, $codes, TRUE)) {
            throw new \InvalidArgumentException(sprintf('Unknown code "%d". You can use "%s".', $code, implode(',', $codes)));
        }
        return $code;
    }



    /**
     * @return array
     */
    public function getResponseArray() : array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
        ];
    }

}