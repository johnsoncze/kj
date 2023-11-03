<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\PriceInfo;

use App\Customer\Customer;
use App\Product\ProductDTO;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class PriceInfo extends Control
{


    /** @var Customer|null */
    private $customer;

    /** @var ProductDTO|null */
    private $product;



    /**
     * @param $customer Customer
     * @return self
     */
    public function setCustomer(Customer $customer) : self
    {
        $this->customer = $customer;
        return $this;
    }



    /**
     * @param $product ProductDTO
     * @return self
     */
    public function setProduct(ProductDTO $product) : self
    {
        $this->product = $product;
        return $this;
    }



    public function render()
    {
        $this->template->customer = $this->customer;
        $this->template->product = $this->product;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}