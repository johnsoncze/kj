<?php

declare(strict_types = 1);

namespace App\Delivery\Translation;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface DeliveryTranslationRepositoryFactory
{


    /**
     * @return DeliveryTranslationRepository
     */
    public function create();
}