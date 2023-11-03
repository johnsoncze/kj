<?php

declare(strict_types = 1);

namespace App\Product\Related;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class RelatedDuplication
{


    /**
     * Check if exists some duplicate related product.
     * @param $related Related
     * @param $relatedRepo RelatedRepository
     * @return Related
     * @throws RelatedDuplicationException
     */
    public function check(Related $related, RelatedRepository $relatedRepo) : Related
    {
        $duplication = $relatedRepo->findOneByProductIdAndProductRelatedIdAndType($related->getProductId(), $related->getRelatedProductId(), $related->getType());
        if ($duplication) {
            throw new RelatedDuplicationException('Související produkt již existuje.');
        }
        return $related;
    }
}