<?php

declare(strict_types = 1);

namespace App\Payment\Translation;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PaymentTranslationRepositoryFactory
{


    /**
     * @return PaymentTranslationRepository
     */
    public function create();
}