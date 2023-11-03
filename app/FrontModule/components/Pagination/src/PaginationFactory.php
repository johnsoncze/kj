<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Pagination;

use Nette\Utils\Paginator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PaginationFactory
{


    /**
     * @param $paginator Paginator
     * @return Pagination
     */
    public function create(Paginator $paginator);
}