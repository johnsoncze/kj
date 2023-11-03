<?php

declare(strict_types = 1);

namespace App\ComGate\Request;

use App\ComGate\Config;
use App\Order\Order;
use Nette\Utils\ArrayHash;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ParameterFactory
{


    /** @var Config */
    protected $config;



    public function __construct(Config $config)
    {
        $this->config = $config;
    }



    /**
     * @param $order Order
     * @return ArrayHash
     */
    public function createCreateParametersFromOrder(Order $order) : ArrayHash
    {
        $parameters = new ArrayHash();
        $parameters->merchant = $this->config->getStoreId();
        $parameters->test = $this->config->isTest() === TRUE ? 'true' : 'false';
        $parameters->country = 'CZ';
        $parameters->price = $order->getSummaryPrice() * 100;
        $parameters->curr = 'CZK';
        $parameters->refId = $order->getToken();
        $parameters->payerId = $order->getCustomerId();
        $parameters->method = 'ALL';
        $parameters->label = $order->getCode();
        $parameters->email = $order->getCustomerEmail();
        $parameters->phone = $order->getCustomerTelephone();
        $parameters->name = 'eshop';
        $parameters->lang = 'cs';
        $parameters->prepareOnly = 'true';
        $parameters->secret = $this->config->getSecret();

        return $parameters;
    }
}