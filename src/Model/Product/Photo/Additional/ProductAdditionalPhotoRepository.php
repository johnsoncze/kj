<?php

declare(strict_types = 1);

namespace App\Product\AdditionalPhoto;

use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductAdditionalPhotoRepository extends BaseRepository
{


    /** @var string */
    protected $entityName = ProductAdditionalPhoto::class;



    /**
     * @param int $id
     * @return ProductAdditionalPhoto
     * @throws ProductAdditionalPhotoNotFoundException
     */
    public function getOneById(int $id) : ProductAdditionalPhoto
    {
        $result = $this->findOneBy([
            'where' => [
                ['id', '=', $id]
            ]
        ]);
        if (!$result) {
            throw new ProductAdditionalPhotoNotFoundException(sprintf('Fotografie s id "%d" nebyla nalezena.', $id));
        }
        return $result;
    }



    /**
     * @param int $productId
     * @return ProductAdditionalPhoto[]|array
     */
    public function findByProductId(int $productId)
    {
        return $this->findBy([
            'where' => [
                ['productId', '=', $productId]
            ]
        ]) ?: [];
    }

}