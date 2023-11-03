<?php

declare(strict_types = 1);

namespace App\Page;

use App\NotFoundException;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageRemoveFacade extends NObject
{


    /** @var PageRepositoryFactory */
    protected $pageRepositoryFactory;



    public function __construct(PageRepositoryFactory $pageRepositoryFactory)
    {
        $this->pageRepositoryFactory = $pageRepositoryFactory;
    }



    /**
     * @param $id int
     * @return int
     * @throws PageRemoveFacadeException
     */
    public function remove(int $id) : int
    {
        try {
            $pageRepository = $this->pageRepositoryFactory->create();
            $page = $pageRepository->getOneById($id, FALSE);
            return $pageRepository->remove($page);
        } catch (NotFoundException $exception) {
            throw new PageRemoveFacadeException($exception->getMessage());
        }
    }
}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageRemoveFacadeException extends \Exception
{


}