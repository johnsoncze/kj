<?php

declare(strict_types = 1);

namespace App\ComGate\Request;

use App\ComGate\Config;
use App\ComGate\Payment\Payment;
use App\ComGate\Response\ResponseParser;
use App\GuzzleHttp\Guzzle\Client;
use App\GuzzleHttp\Guzzle\ClientException;
use GuzzleHttp\RequestOptions;
use Kdyby\Monolog\Logger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class RequestSender
{


    /** @var Client */
    protected $client;

    /** @var Config */
    protected $config;

    /** @var Logger */
    protected $logger;

    /** @var ResponseParser */
    protected $responseParser;



    public function __construct(Client $client,
                                Config $config,
                                Logger $logger,
                                ResponseParser $responseParser)
    {
        $this->client = $client;
        $this->config = $config;
        $this->logger = $logger;
        $this->responseParser = $responseParser;
    }



    /**
     * @param $params array
     * @return Payment
     * @throws \InvalidArgumentException
     * @throws ClientException
     */
    public function createPayment(array $params) : Payment
    {
        $url = $this->config->getUrl() . DIRECTORY_SEPARATOR . 'create';
        $response = $this->client->send($url, [RequestOptions::FORM_PARAMS => $params], 'POST', FALSE);
        $response = $this->responseParser->parseString($response->getBody()->getContents());

        try {
            return Payment::createFromApiResponse($response);
        } catch (\InvalidArgumentException $exception) {
            $this->logger->addError('comgate.api.response: ' . $exception->getMessage(), ['url' => $url, 'requestParameters' => $params, 'response' => $response]);
            throw $exception;
        }
    }
}