<?php

declare(strict_types = 1);

namespace App\Opportunity;

use App\Opportunity\Email\SendEmailFacade;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface SendEmailFacadeFactory
{


    /**
     * @return SendEmailFacade
     */
    public function create();
}