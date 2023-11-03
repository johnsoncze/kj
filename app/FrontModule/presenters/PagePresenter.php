<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Catalog\Catalog;
use App\FrontModule\Components\Breadcrumb\Item;
use App\FrontModule\Components\Catalog\CatalogList\CatalogList;
use App\FrontModule\Components\Catalog\CatalogList\CatalogListFactory;
use App\FrontModule\Components\Page\StaticPage\AboutUs\AboutUs;
use App\FrontModule\Components\Page\StaticPage\AboutUs\AboutUsFactory;
use App\FrontModule\Components\Page\StaticPage\Contact\Contact;
use App\FrontModule\Components\Page\StaticPage\Contact\ContactFactory;
use App\FrontModule\Components\Page\StaticPage\Meeting\Meeting;
use App\FrontModule\Components\Page\StaticPage\Meeting\MeetingFactory;
use App\FrontModule\Components\Page\StaticPage\CustomProduction\CustomProduction;
use App\FrontModule\Components\Page\StaticPage\CustomProduction\CustomProductionFactory;
use App\FrontModule\Components\Page\StaticPage\GoldsmithWorkshop\GoldsmithWorkshop;
use App\FrontModule\Components\Page\StaticPage\GoldsmithWorkshop\GoldsmithWorkshopFactory;
use App\FrontModule\Components\Page\StaticPage\Services\Services;
use App\FrontModule\Components\Page\StaticPage\Services\ServicesFactory;
use App\FrontModule\Components\Page\StaticPage\Showroom\Showroom;
use App\FrontModule\Components\Page\StaticPage\Showroom\ShowroomFactory;
use App\FrontModule\Components\Page\StaticPage\Team\Team;
use App\FrontModule\Components\Page\StaticPage\Team\TeamFactory;
use App\NotFoundException;
use App\Page\PageEntity;
use App\Page\PageRepository;
use Nette\Application\BadRequestException;
use Nette\Application\LinkGenerator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class PagePresenter extends AbstractPresenter
{


    /** @var AboutUsFactory @inject */
    public $aboutUsFactory;

    /** @var CatalogListFactory @inject */
    public $catalogListFactory;

    /** @var ContactFactory @inject */
    public $contactPageFactory;

    /** @var MeetingFactory @inject */
    public $meetingFactory;
		
    /** @var CustomProductionFactory @inject */
    public $customProductionPageFactory;

    /** @var GoldsmithWorkshopFactory @inject */
    public $goldsmithWorkshopPageFactory;

    /** @var PageEntity|null */
    public $page;

    /** @var PageRepository @inject */
    public $pageRepo;

    /** @var ServicesFactory @inject */
    public $servicesPageFactory;

    /** @var ShowroomFactory @inject */
    public $showroomPageFactory;

    /** @var TeamFactory @inject */
    public $teamPageFactory;



    /**
     * @param $url string
     * @return void
     * @throws BadRequestException
     */
    public function actionDetail(string $url)
    {
        try {
            $this->page = $this->pageRepo->getOnePublishedByUrlAndLanguageIdAndType($url, $this->language->getId());
            $subPages = $this->pageRepo->findPublishedByMoreParentId([$this->page->getId()]);
            $subPages ? $this->page->setSubPages($subPages) : NULL;
            $this->setBreadcrumb($this->page);

            $this->template->title = $this->page->getResolvedTitle();
            $this->template->metaDescription = $this->page->getDescriptionSeo();
            $this->template->page = $this->page;
            $this->template->childPages = $subPages;
            $this->page->getTemplate() ? $this->template->setFile($this->page->getTemplatePath()) : NULL;
            $this->template->ogTitle = $this->page->getTitleOg();
            $this->template->ogDescription = $this->page->getDescriptionOg();
        } catch (NotFoundException $exception) {
            throw new BadRequestException(NULL, 404);
        }
    }



    /**
     * @return CatalogList
     */
    public function createComponentEmployeeList() : CatalogList
    {
        $list = $this->catalogListFactory->create();
        $list->setType(Catalog::TYPE_EMPLOYEE);
        return $list;
    }



    /**
     * @return CatalogList
    */
    public function createComponentJobVacancyList() : CatalogList
    {
        $list = $this->catalogListFactory->create();
        $list->setType(Catalog::TYPE_JOB_VACANCY);
        return $list;
    }

    /**
     * @return CatalogList
     */
    public function createComponentRepresentativeCatalogList() : CatalogList
    {
        $list = $this->catalogListFactory->create();
        $list->setType(Catalog::TYPE_REPRESENTATIVE_CATALOG);
        return $list;
    }


		
    /**
     * @return MeetingForm
     */
    public function createComponentMeetingForm() : Meeting
    {
        return $this->meetingFactory->create();
    }
		
		
    /**
     * @param $page PageEntity
     * @return PageEntity
    */
    private function setBreadcrumb(PageEntity $page) : PageEntity
    {
        $parentPage = $page->getParentPage();
        if ($parentPage) {
            $this->setBreadcrumb($parentPage);
        }
        $this->breadcrumb->addItem(new Item($page->getName(), $page->getFrontendLink($this->context->getByType(LinkGenerator::class))));
        return $page;
    }


    /** ----------------- STATIC PAGE FACTORIES ----------------- **/


    /**
     * @return AboutUs
     */
    public function createComponentAboutUs() : AboutUs
    {
        return $this->aboutUsFactory->create();
    }



    /**
     * @return Contact
     */
    public function createComponentContactPage() : Contact
    {
        return $this->contactPageFactory->create();
    }

		

    /**
     * @return CustomProduction
     */
    public function createComponentCustomProductionPage() : CustomProduction
    {
        return $this->customProductionPageFactory->create();
    }



    /**
     * @return GoldsmithWorkshop
     */
    public function createComponentGoldsmithWorkshopPage() : GoldsmithWorkshop
    {
        return $this->goldsmithWorkshopPageFactory->create();
    }



    /**
     * @return Services
     */
    public function createComponentServicesPage() : Services
    {
        return $this->servicesPageFactory->create();
    }



    /**
     * @return Showroom
     */
    public function createComponentShowroomPage() : Showroom
    {
        return $this->showroomPageFactory->create();
    }



    /**
     * @return Team
     */
    public function createComponentTeamPage() : Team
    {
        return $this->teamPageFactory->create();
    }
}
