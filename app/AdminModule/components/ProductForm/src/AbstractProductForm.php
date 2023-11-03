<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductForm;

use App\Components\TranslationFormTrait;
use App\Product\Product;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class AbstractProductForm extends Control
{


    use TranslationFormTrait;

    /** @var ProductFormRemovePhotoFactory */
    protected $productFormRemovePhotoFactory;

    /** @var Product|null */
    protected $product;

    /** @var string|null */
    protected $type;



    public function __construct(ProductFormRemovePhotoFactory $productFormRemovePhotoFactory)
    {
        parent::__construct();
        $this->productFormRemovePhotoFactory = $productFormRemovePhotoFactory;
    }



    /**
     * @param $product Product
     * @return self
     */
    public function setProduct(Product $product) : self
    {
        $this->product = $product;
        return $this;
    }



    /**
     * @return Product|null
     */
    public function getProduct()
    {
        return $this->product;
    }



    /**
     * @param $type string
     * @return self
     */
    public function setType(string $type) : self
    {
        $this->type = $type;
        return $this;
    }



    /**
     * @return void
     */
    public function handleRemoveMainPhoto()
    {
        $photoRemove = $this->productFormRemovePhotoFactory->create();
        $photoRemove->removeMainPhoto($this);
    }



    /**
     * @param int $id
     */
    public function handleRemoveAdditionalPhoto(int $id)
    {
        $photoRemove = $this->productFormRemovePhotoFactory->create();
        $photoRemove->removeAdditionalPhoto($id, $this);
    }



    public function render()
    {
        $product = $this->getProduct();
        $this->template->product = $product;
        $this->template->type = $product ? $product->getType() : $this->type;
    }
}