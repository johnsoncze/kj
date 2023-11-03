<?php

declare(strict_types = 1);

namespace App\GuzzleHttp\Guzzle;

use GuzzleHttp\Exception\ClientException;
use Kdyby\Monolog\Logger;
use Psr\Http\Message\ResponseInterface;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * Wrapper for Client object.
 */
final class Client
{


    /** @var \GuzzleHttp\Client */
    private $client;

    /** @var Logger */
    private $logger;



    public function __construct(\GuzzleHttp\Client $client,
                                Logger $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }



    /**
     * @param $url string
     * @param $params array
     * @param $method string
     * @param $encodeJson bool
     * @return array|ResponseInterface
     * @throws \App\GuzzleHttp\Guzzle\ClientException
     */
    public function send(string $url, array $params = [], string $method = 'GET', bool $encodeJson = TRUE)
    {
        $request = [
            'method' => $method,
            'url' => $url,
            'params' => $params,
        ];

        try {
            $this->logger->addDebug('Sending API request.', ['request' => $request]);
            $response = $this->client->request($method, $url, $params);
            $printedResponse = print_r($response, TRUE);
            $this->logger->addDebug('API request success finished.', ['request' => $request, 'response' => $printedResponse]);
            if ($response->getStatusCode() !== 200) {
                $this->logger->addWarning('API request was not finished with code 200.', ['request', 'response' => $printedResponse]);
            }
            return $encodeJson === TRUE ? $this->responseJsonToArray($response) : $response;
        } catch (ClientException $exception) {
            $message = sprintf('API request failed. Error: %s', $exception->getMessage());
            $this->logger->addError($message, ['request' => $request]);
            throw new \App\GuzzleHttp\Guzzle\ClientException($message, 0, $exception);
        }
    }



    /**
     * @param $response ResponseInterface
     * @return array
     * @throws \App\GuzzleHttp\Guzzle\ClientException
     */
    public function responseJsonToArray(ResponseInterface $response) : array
    {
        $body = $response->getBody();

        try {
            return \GuzzleHttp\json_decode($body, TRUE);
        } catch (\InvalidArgumentException $exception) {
            $message = sprintf('Response is not valid. Error: %s', $exception->getMessage());
            $this->logger->addError($message, ['body' => $body]);
            throw new \App\GuzzleHttp\Guzzle\ClientException($message);
        }
    }
}