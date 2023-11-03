<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\NewsletterSubscriptionForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface FormFactory
{


    /**
     * @return Form
     */
    public function create();
}