<?php

declare(strict_types = 1);

namespace App\Tests\Payment\Translation;

use App\Payment\Translation\PaymentTranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait PaymentTranslationTestTrait
{


    /**
     * @return PaymentTranslation
     */
    private function createTestPaymentTranslation() : PaymentTranslation
    {
        $translation = new PaymentTranslation();
        $translation->setPaymentId(1);
        $translation->setLanguageId(1);
        $translation->setName('Payment');

        return $translation;
    }
}