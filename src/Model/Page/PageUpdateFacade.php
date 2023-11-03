<?php

declare(strict_types = 1);

namespace App\Page;

use App\NotFoundException;
use App\Url\UrlResolver;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageUpdateFacade extends NObject
{


    /** @var PageRepositoryFactory */
    protected $pageRepositoryFactory;

    /** @var UrlResolver */
    protected $urlResolver;



    public function __construct(PageRepositoryFactory $pageRepositoryFactory,
                                UrlResolver $urlResolver)
    {
        $this->pageRepositoryFactory = $pageRepositoryFactory;
        $this->urlResolver = $urlResolver;
    }



    /**
     * @param PageEntity $page
     * @return PageEntity
     * @throws PageUpdateFacadeException
     */
    public function update(PageEntity $page) : PageEntity
    {
        if (!$page->getId()) {
            throw new PageUpdateFacadeException("For a new page use '" . get_class(PageAddFacade::class) . "'.");
        }

        try {
            $pageRepository = $this->pageRepositoryFactory->create();
            $pageFromDb = $pageRepository->getOneById($page->getId(), FALSE);

            if ($page->getUrl() !== $pageFromDb->getUrl()) {
                $page->setUrl($this->urlResolver->getAvailableUrl($page->getUrl() ?: $page->getName(), $pageRepository, (int)$page->getLanguageId()));
            }

            //Check duplicity
            $pageByName = $pageRepository->findOneByNameAndLangId($page->getName(), $page->getLanguageId());
            $pageByUrl = $pageRepository->findOneByUrlAndLangId($page->getUrl(), $page->getLanguageId());
            $duplicateService = new PageDuplicateService();
            $duplicateService->checkName($page, $pageByName);
            $duplicateService->checkUrl($page, $pageByUrl);

            //Save
            $pageRepository->save($page);

            //Check depth of parent
            $pageDepth = new PageParentDepth();
            $pageDepth->checkDepth($pageFromDb);

            return $page;
        } catch (PageDuplicateServiceException $exception) {
            throw new PageUpdateFacadeException($exception->getMessage());
        } catch (PageParentDepthException $exception) {
            throw new PageUpdateFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $pageId int
     * @param $moduleId int
     * @return PageEntity
     * @throws PageUpdateFacadeException
     */
    public function updateFromParameters(int $pageId, int $moduleId) : PageEntity
    {
        $pageRepo = $this->pageRepositoryFactory->create();
        $pageWithSameModule = $pageRepo->findOneArticlesTypeByArticleModuleId($moduleId);

        try {
            $page = $pageRepo->getOneById($pageId, FALSE);
            if ($pageWithSameModule instanceof PageEntity && $pageWithSameModule->getId() !== $page->getId()) {
                throw new PageUpdateFacadeException('Stránka s nastaveným modulem již existuje.');
            }
            $page->setArticleModuleId($moduleId);
            $pageRepo->save($page);
            return $page;
        } catch (NotFoundException $exception) {
            throw new PageUpdateFacadeException($exception->getMessage());
        }
    }
}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageUpdateFacadeException extends \Exception
{


}