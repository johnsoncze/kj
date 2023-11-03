<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Pagination;

use Nette\Utils\Paginator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PaginatorFactory
{


    /**
     * @param $itemCount int
     * @param $perPage int
     * @param $actualPage int
     * @return Paginator
     */
    public static function create(int $itemCount, int $perPage, int $actualPage) : Paginator
    {
        $paginator = new Paginator();
        $paginator->setPage($actualPage);
        $paginator->setItemsPerPage($perPage);
        $paginator->setItemCount($itemCount);

        return $paginator;
    }
}