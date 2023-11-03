<?php

declare(strict_types = 1);

namespace App\Tests\Product\Translation;

use App\Product\Translation\ProductTranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ProductTranslationTestTrait
{


    /**
     * Create object with dummy data.
     * @return ProductTranslation
     */
    public function createTestProductTranslation() : ProductTranslation
    {
        $translation = new ProductTranslation();
        $translation->setProductId(1);
        $translation->setLanguageId(1);
        $translation->setName('Produkt 1');
        $translation->setDescription('Description of product.');
        $translation->setUrl('produkt-1');

        return $translation;
    }
}