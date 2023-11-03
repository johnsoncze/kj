<?php

declare(strict_types = 1);

namespace App\Product;

use Ricaefeliz\Mappero\Translation\Localization;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ProductListTrait
{


    /**
     * Get product list.
     * @param $repo ProductRepository
     * @param $localization Localization
     * @param $excludeId array exclude some products by id
     * @return Product[]|array
     */
    public function getProductList(ProductRepository $repo,
                                   Localization $localization,
                                   array $excludeId = []) : array
    {
        $list = $repo->findListByLanguageId($localization->getId());
        $excludeId = $list && $excludeId ? $excludeId : [];
        foreach ($excludeId as $id) {
            unset($list[$id]);
        }
        return $list;
    }
}