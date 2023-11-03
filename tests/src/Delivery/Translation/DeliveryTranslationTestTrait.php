<?php

declare(strict_types = 1);

namespace App\Tests\Delivery\Translation;

use App\Delivery\Translation\DeliveryTranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait DeliveryTranslationTestTrait
{


    /**
     * @return DeliveryTranslation
     */
    private function createTestDeliveryTranslation() : DeliveryTranslation
    {
        $translation = new DeliveryTranslation();
        $translation->setDeliveryId(1);
        $translation->setLanguageId(1);
        $translation->setName('Delivery');

        return $translation;
    }
}