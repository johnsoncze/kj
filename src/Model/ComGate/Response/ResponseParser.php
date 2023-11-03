<?php

declare(strict_types = 1);

namespace App\ComGate\Response;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ResponseParser
{


    /**
     * @param $response string
     * @return array
     */
    public function parseString(string $response) : array
    {
        parse_str($response, $array);
        return $array;
    }
}