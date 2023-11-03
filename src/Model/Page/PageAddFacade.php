<?php

declare(strict_types = 1);

namespace App\Page;

use App\Language\LanguageRepositoryFactory;
use App\NotFoundException;
use App\Url\UrlResolver;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 */
class PageAddFacade extends NObject
{


    /** @var LanguageRepositoryFactory */
    protected $languageRepositoryFactory;

    /** @var PageRepositoryFactory */
    protected $pageRepositoryFactory;

    /** @var UrlResolver */
    protected $urlResolver;



    public function __construct(LanguageRepositoryFactory $languageRepositoryFactory,
                                PageRepositoryFactory $pageRepositoryFactory,
                                UrlResolver $urlResolver)
    {
        $this->languageRepositoryFactory = $languageRepositoryFactory;
        $this->pageRepositoryFactory = $pageRepositoryFactory;
        $this->urlResolver = $urlResolver;
    }



    /**
     * @param int $languageId
     * @param int|NULL $parentPageId
     * @param string $type
     * @param string $name
     * @param string|NULL $content
     * @param string|NULL $url
     * @param string|NULL $titleSeo
     * @param string|NULL $descriptionSeo
     * @param array|NULL $setting
     * @param string $status
     * @param $template string|null
     * @param $menuLocation int
     * @return PageEntity
     * @throws PageAddFacadeException
     */
    public function add(int $languageId, int $parentPageId = NULL, string $type, string $name, string $content = NULL, string $url = NULL,
                        string $titleSeo = NULL, string $descriptionSeo = NULL, array $setting = NULL, string $status, string $template = NULL,
                        int $menuLocation)
    {
        try {
            //Check language
            $languageRepository = $this->languageRepositoryFactory->create();
            $languageRepository->getOneById($languageId);

            $pageRepository = $this->pageRepositoryFactory->create();

            //Create page entity
            $pageFactory = new PageEntityFactory();
            $page = $pageFactory->create($languageId, $parentPageId, $type, $name, $content, $url, $titleSeo, $descriptionSeo, $setting, $status);
            $page->setTemplate($template);
            $page->setMenuLocation($menuLocation);
            $page->setUrl($this->urlResolver->getAvailableUrl($url ?: $name, $pageRepository, $languageId));

            //Check duplicity
            $pageByName = $pageRepository->findOneByNameAndLangId($page->getName(), $page->getLanguageId());
            $pageByUrl = $pageRepository->findOneByUrlAndLangId($page->getUrl(), $page->getLanguageId());
            $duplicateService = new PageDuplicateService();
            $duplicateService->checkName($page, $pageByName);
            $duplicateService->checkUrl($page, $pageByUrl);

            //Save
            $pageRepository->save($page);

            //Check depth of parent
            $pageFromDb = $pageRepository->getOneById($page->getId(), FALSE);
            $pageDepth = new PageParentDepth();
            $pageDepth->checkDepth($pageFromDb);

            return $page;

        } catch (NotFoundException $exception) {
            throw new PageAddFacadeException($exception->getMessage());
        } catch (PageDuplicateServiceException $exception) {
            throw new PageAddFacadeException($exception->getMessage());
        } catch (PageParentDepthException $exception) {
            throw new PageAddFacadeException($exception->getMessage());
        }
    }
}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageAddFacadeException extends \Exception
{


}