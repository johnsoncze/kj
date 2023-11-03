<?php

declare(strict_types = 1);

namespace App\Product\WeedingRing\Size;

use App\NotFoundException;
use App\Product\ProductNotFoundException;
use App\Product\ProductRepository;
use App\Product\Ring\Size\SizeRepository AS RingSizeRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SizeFacade
{


    /** @var ProductRepository */
    private $productRepo;

    /** @var RingSizeRepository */
    private $ringSizeRepo;

    /** @var SizeRepository */
    private $sizeRepo;



    public function __construct(ProductRepository $productRepository,
                                RingSizeRepository $ringSizeRepository,
                                SizeRepository $sizeRepo)
    {
        $this->productRepo = $productRepository;
        $this->ringSizeRepo = $ringSizeRepository;
        $this->sizeRepo = $sizeRepo;
    }



    /**
     * @param $productId int
     * @param $gender string
     * @param $sizeId int
     * @param $price float
     * @param $vat float
     * @return Size
     * @throws SizeFacadeException
     */
    public function save(int $productId,
                         int $sizeId,
                         string $gender,
                         float $price,
                         float $vat = 21.0) : Size
    {
        try {
            $product = $this->productRepo->getOneById($productId);
            $ringSize = $this->ringSizeRepo->getOneById($sizeId);

            $sizeObject = $this->sizeRepo->findOneByProductIdAndGenderAndSizeId($product->getId(), $gender, $ringSize->getId()) ?: new Size();
            $sizeObject->setProductId($product->getId());
            $sizeObject->setSizeId($sizeId);
            $sizeObject->setGender($gender);
            $sizeObject->setPrice($price);
            $sizeObject->setVat($vat);
            $this->sizeRepo->save($sizeObject);

            return $sizeObject;
        } catch (NotFoundException $exception) {
            throw new SizeFacadeException($exception->getMessage());
        } catch (ProductNotFoundException $exception) {
            throw new SizeFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $id int
     * @return bool
     * @throws SizeFacadeException
     */
    public function remove(int $id) : bool
    {
        try {
            $size = $this->sizeRepo->getOneById($id);
            $this->sizeRepo->remove($size);
            return TRUE;
        } catch (NotFoundException $exception) {
            throw new SizeFacadeException($exception->getMessage());
        }
    }
}