<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductForm;

use App\Libs\FileManager\Responses\DeleteFileResponse;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeException;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeFactory;
use App\Product\ProductSaveFacadeException;
use App\Product\ProductSaveFacadeFactory;
use Nette\Database\Context;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductFormRemovePhoto extends NObject
{


    /** @var Context */
    protected $database;

	/** @var ProductAdditionalPhotoSaveFacadeFactory */
	protected $productAdditionalPhotoFacadeFactory;

    /** @var ProductSaveFacadeFactory */
    protected $productFacadeFactory;



    public function __construct(Context $database,
								ProductAdditionalPhotoSaveFacadeFactory $productAdditionalPhotoFacadeFactory,
								ProductSaveFacadeFactory $productSaveFacadeFactory)
    {
        $this->database = $database;
        $this->productAdditionalPhotoFacadeFactory = $productAdditionalPhotoFacadeFactory;
        $this->productFacadeFactory = $productSaveFacadeFactory;
    }



    /**
     * @param AbstractProductForm $productForm
     */
    public function removeMainPhoto(AbstractProductForm $productForm)
    {
        $presenter = $productForm->getPresenter();

        if ($presenter->isAjax() === TRUE) {
            try {
                $this->database->beginTransaction();
                $removeFacade = $this->productFacadeFactory->create();
                $removeFacade->deletePhoto($productForm->getProduct()->getId());
                $this->database->commit();
                $response = new DeleteFileResponse('Fotografie byla smazána.', DeleteFileResponse::SUCCESS);
            } catch (ProductSaveFacadeException $exception) {
                $this->database->rollBack();
                $response = new DeleteFileResponse($exception->getMessage(), DeleteFileResponse::ERROR);
            }
            $presenter->sendJson($response->getResponseArray());
        }
    }



    /**
     * @param int $photoId
     * @param AbstractProductForm $productForm
     */
    public function removeAdditionalPhoto(int $photoId, AbstractProductForm $productForm)
    {
        $presenter = $productForm->getPresenter();
        if ($presenter->isAjax() === TRUE) {
            try {
                $this->database->beginTransaction();
                $removeFacade = $this->productAdditionalPhotoFacadeFactory->create();
                $removeFacade->remove($photoId);
                $this->database->commit();
                $response = new DeleteFileResponse('Fotografie byla smazána.', DeleteFileResponse::SUCCESS);
            } catch (ProductAdditionalPhotoSaveFacadeException $exception) {
                $this->database->rollBack();
                $response = new DeleteFileResponse($exception->getMessage(), DeleteFileResponse::ERROR);
            }
            $presenter->sendJson($response->getResponseArray());
        }
    }
}