<?php

declare(strict_types = 1);

namespace App\Product\Translation;

use App\Product\ProductExistsAlreadyException;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductTranslationDuplicate extends NObject
{


    /**
     * @param ProductTranslation $product
     * @param ProductTranslation|NULL $duplicateProduct
     * @return ProductTranslation
     * @throws ProductExistsAlreadyException
     */
    public function checkUrl(ProductTranslation $product, ProductTranslation $duplicateProduct = NULL) : ProductTranslation
    {
        if ($duplicateProduct !== NULL
            && (int)$product->getId() !== (int)$duplicateProduct->getId()
            && (int)$product->getLanguageId() === (int)$duplicateProduct->getLanguageId()
            && $product->getUrl() === $duplicateProduct->getUrl()
        ) {
            throw new ProductExistsAlreadyException(sprintf('Produkt s url "%s" pro jazyk s id "%d" již existuje.',
                $product->getUrl(), $product->getLanguageId()));
        }
        return $product;
    }
}