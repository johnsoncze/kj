<?php

declare(strict_types = 1);

namespace App\Page;

use App\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class PageFacade
{


    /** @var PageRepository */
    private $pageRepo;



    public function __construct(PageRepository $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }



    /**
     * @param $sorting array [pageId => sorting,..]
     * @return bool
     * @throws PageFacadeException
     */
    public function saveSort(array $sorting) : bool
    {
        $pageId = array_keys($sorting);
        $pages = $this->pageRepo->findByMoreId($pageId);

        foreach ($sorting as $_pageId => $_sorting) {
            $page = $pages[$_pageId] ?? NULL;
            if ($page === NULL) {
                throw new PageFacadeException(sprintf('Nebyla nalezena stránka s id \'%d\'.', $_pageId));
            }
            $page->setSort($_sorting);
            $this->pageRepo->save($page);
        }

        return TRUE;
    }



    /**
     * @param $langId int
     * @param $menu int
     * @return PageEntity[]|array
     */
    public function findPublishedParentsByLanguageIdAndMenuLocation(int $langId, int $menu) : array
    {
        $pages = $this->pageRepo->findPublishedParentsByLanguageIdAndMenuLocation($langId, $menu);
        if ($pages) {
            $pageId = Entities::getProperty($pages, 'id');
            $subPages = $this->pageRepo->findPublishedByMoreParentId($pageId);
            $subPages = $subPages ? Entities::toSegment($subPages, 'parentPageId') : [];
            foreach ($subPages as $_pageId => $_subPages) {
                $pages[$_pageId]->setSubPages($_subPages);
            }
        }
        return $pages;
    }
}