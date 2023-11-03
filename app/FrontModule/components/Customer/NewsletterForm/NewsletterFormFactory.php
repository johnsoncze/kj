<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Customer\NewsletterForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface NewsletterFormFactory
{


    /**
     * @return NewsletterForm
     */
    public function create();
}