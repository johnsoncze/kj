<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\Contact;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ContactFactory
{


    /**
     * @return Contact
     */
    public function create();
}