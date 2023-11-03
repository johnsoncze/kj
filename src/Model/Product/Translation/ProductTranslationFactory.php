<?php

declare(strict_types = 1);

namespace App\Product\Translation;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductTranslationFactory extends NObject
{


    /**
     * @param int $productId
     * @param int $languageId
     * @param string $name
     * @param string|NULL $description
     * @param string|NULL $url
     * @param string|NULL $titleSeo
     * @param string|NULL $descriptionSeo
     * @return ProductTranslation
     */
    public function create(
        int $productId,
        int $languageId,
        string $name,
        string $description = null,
        string $url = null,
        string $titleSeo = null,
        string $descriptionSeo = null,
        string $titleOg = null,
        string $descriptionOg = null
    ): ProductTranslation {
        $productTranslation = new ProductTranslation();
        $productTranslation->setProductId($productId);
        $productTranslation->setLanguageId($languageId);
        $productTranslation->setName($name);
        $productTranslation->setDescription($description);
        $productTranslation->setUrl($url);
        $productTranslation->setTitleSeo($titleSeo);
        $productTranslation->setDescriptionSeo($descriptionSeo);
        $productTranslation->setTitleOg($titleOg);
        $productTranslation->setDescriptionOg($descriptionOg);
        $productTranslation->setAddDate(new \DateTime());
        return $productTranslation;
    }
}