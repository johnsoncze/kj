<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\PhotoGallery;

use App\Product\AdditionalPhoto\ProductAdditionalPhotoRepository;
use App\Product\Product;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class PhotoGallery extends Control
{


    /** @var Product|null */
    private $product;

    /** @var ProductAdditionalPhotoRepository */
    private $additionalPhotoRepo;



    public function __construct(ProductAdditionalPhotoRepository $productAdditionalPhotoRepo)
    {
        parent::__construct();
        $this->additionalPhotoRepo = $productAdditionalPhotoRepo;
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
	 * @param $displayPackageImage int
	 * @return self
	 */
	public function setDisplayPackageImage($displayPackageImage) : self
	{
		$this->displayPackageImage = $displayPackageImage;
		return $this;
	}



    /**
     * @return void
     */
    public function render()
    {
        $this->template->additionalPhotos = $this->additionalPhotoRepo->findByProductId($this->product->getId()) ?: [];
        $this->template->product = $this->product;
        $this->template->displayPackageImage = $this->displayPackageImage;
        $this->template->setFile(__DIR__ . '/templates/default.latte');
        $this->template->render();
    }



    /**
     * @return void
     */
    public function renderMobile()
    {
        $this->template->additionalPhotos = $this->additionalPhotoRepo->findByProductId($this->product->getId()) ?: [];
        $this->template->product = $this->product;
	    $this->template->displayPackageImage = $this->displayPackageImage;
        $this->template->setFile(__DIR__ . '/templates/mobile.latte');
        $this->template->render();
    }
}