<?php

declare(strict_types = 1);

namespace App\Product\Related;

use App\NotFoundException;
use App\Product\Product;
use App\Product\ProductNotFoundException;
use App\Product\ProductRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class RelatedFacade
{


    /** @var ProductRepository */
    private $productRepo;

    /** @var RelatedDuplication */
    private $relatedDuplication;

    /** @var RelatedRepository */
    private $relatedRepo;



    /**
     * RelatedFacade constructor.
     * @param ProductRepository $productRepo
     * @param RelatedDuplication $relatedDuplication
     * @param RelatedRepository $relatedRepo
     */
    public function __construct(ProductRepository $productRepo, RelatedDuplication $relatedDuplication, RelatedRepository $relatedRepo)
    {
        $this->productRepo = $productRepo;
        $this->relatedDuplication = $relatedDuplication;
        $this->relatedRepo = $relatedRepo;
    }



    /**
     * @param $productId int
     * @param $relatedProductId int
     * @param $type string|int
     * @return Related
     * @throws RelatedFacadeException
     */
    public function add(int $productId, int $relatedProductId, $type) : Related
    {
        try {
            $product = $this->productRepo->getOneById($productId);
            $relatedProduct = $this->productRepo->getOneById($relatedProductId);

            $relatedObject = $this->createRelated($product, $relatedProduct, $type);
            $this->relatedDuplication->check($relatedObject, $this->relatedRepo);
            $this->relatedRepo->save($relatedObject);

            if ($type !== Related::CROSS_SELLING) {
                $invertedRelatedObject = $this->createRelated($relatedProduct, $product, $type);
                $invertedRelatedObject->setParentId($relatedObject->getId());
                $this->relatedRepo->save($invertedRelatedObject);
                $relatedObject->setParentId($invertedRelatedObject->getId());
                $this->relatedRepo->save($relatedObject);
            }

            return $relatedObject;
        } catch (RelatedDuplicationException $exception) {
            throw new RelatedFacadeException($exception->getMessage());
        } catch (ProductNotFoundException $exception) {
            throw new RelatedFacadeException($exception->getMessage());
        }
    }



    /**
     * Remove related product.
     * @param $id int
     * @return bool
     * @throws RelatedFacadeException
     */
    public function remove(int $id) : bool
    {
        try {
            $related = $this->relatedRepo->getOneById($id);
            $this->relatedRepo->remove($related);
            return TRUE;
        } catch (NotFoundException $exception) {
            throw new RelatedFacadeException($exception->getMessage());
        }
    }



    /**
     * Create Related object.
     * @param $product Product
     * @param $relatedProduct Product
     * @param $type string
     * @return Related
     */
    private function createRelated(Product $product, Product $relatedProduct, $type) : Related
    {
        $related = new Related();
        $related->setProductId($product->getId());
        $related->setRelatedProductId($relatedProduct->getId());
        $related->setType($type);

        return $related;
    }
}