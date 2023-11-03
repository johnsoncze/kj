<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\Services;

use App\FrontModule\Components\OpportunityForm\OpportunityForm;
use App\FrontModule\Components\Page\Block\Timeline\Item;
use App\FrontModule\Components\Page\Block\Timeline\Timeline;
use App\FrontModule\Components\Page\Block\Timeline\TimelineFactory;
use Kdyby\Translation\ITranslator;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Services extends Control
{


    /** @var TimelineFactory */
    private $timelineFactory;

    /** @var ITranslator */
    private $translator;



    public function __construct(ITranslator $translator,
                                TimelineFactory $timelineFactory)
    {
        parent::__construct();
        $this->timelineFactory = $timelineFactory;
        $this->translator = $translator;
    }



    /**
     * @return Timeline
     */
    public function createComponentServicesTimeline() : Timeline
    {
        $presenter = $this->getPresenter();

        $item1 = new Item();
        $item1->setReverse(FALSE);
        $item1->setTitle($this->translator->translate('pageStatic.services.selection.title'));
        $item1->setDescription($this->translator->translate('pageStatic.services.selection.description'));
        $item1->setImagesClass(' u-mt--60 u-sm-mt--ofset-2x160');
        $item1->setImages('<img src="/assets/front/images/layout/zlatnicka-dilna-ctime-zlatnicke-remeslo-2.jpg" alt="" class="Timeline-image-primary">
                           <img src="/assets/front/images/layout/prsten-motyl.jpg" alt="" class="Timeline-image-secondary">
                           <span class="Timeline-image-ornament-1"></span>
                           <span class="Timeline-image-ornament-2"></span>');
        $item1->setHtml(sprintf('<div class="u-pt--10"><a href="%s" class="Link Link-big">%s</a></div>',
            $presenter->link('Category:default', ['url' => 'kolekce-jk']),
            $this->translator->translate('pageStatic.services.selection.cta')));

        $item2 = new Item();
        $item2->setReverse(TRUE);
        $item2->setRowClass(' u-mt--30 u-md-mt--50');
        $item2->setContentClass(' u-pt--20 u-sm-pt--70');
        $item2->setDescription($this->translator->translate('pageStatic.services.selection.watch.description'));
        $item2->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item2->setImagesClass(' u-mt--0 u-mb--0');
        $item2->setImages('<img src="/assets/front/images/layout/hodinky-edox.png" alt="" class="Timeline-image-primary">');
        $item2->setHtml(sprintf('<div class="u-pt--10"><a href="%s" class="Link Link-big">%s</a></div>',
            $presenter->link('Category:default', ['url' => 'hodinky']),
            $this->translator->translate('pageStatic.services.selection.watch.cta')));

        $item3 = new Item();
        $item3->setReverse(FALSE);
        $item3->setContentClass(' u-pt--40 u-md-pt--70');
        $item3->setSubtitle($this->translator->translate('pageStatic.services.consultation.title'));
        $item3->setDescription($this->translator->translate('pageStatic.services.consultation.description'));
        $item3->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item3->setImages('<img src="/assets/front/images/layout/kalendar.jpg" alt="" class="Timeline-image-primary">');
        $item3->setHtml(sprintf('<div class="u-pt--10"><a href="#%s" class="Link Link-big js-popup-opener">%s</a></div>',
			OpportunityForm::PRODUCT_STORE_MEETING_POPUP_ID,
            $this->translator->translate('pageStatic.services.consultation.cta')));

        $item4 = new Item();
        $item4->setReverse(TRUE);
        $item4->setRowClass(' u-mt--30 u-md-mt--50');
        $item4->setContentClass(' u-pt--20 u-sm-pt--70');
        $item4->setSubtitle($this->translator->translate('pageStatic.services.customProduction.title'));
        $item4->setDescription($this->translator->translate('pageStatic.services.customProduction.description'));
        $item4->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item4->setImagesClass(' u-mt--0 u-mb--0');
        $item4->setImages('<img src="/assets/front/user_content/images/nahrdelnik-nausnice-voda.jpg" alt="" class="Timeline-image-primary">');
        $item4->setHtml(sprintf('<div class="u-pt--10"><a href="%s" class="Link Link-big">%s</a></div>',
            $presenter->link('Page:detail', ['url' => 'zakazkova-vyroba']),
            $this->translator->translate('pageStatic.services.customProduction.cta')));

        $timeline = $this->timelineFactory->create();
        $timeline->addItem($item1);
        $timeline->addItem($item2);
        $timeline->addItem($item3);
        $timeline->addItem($item4);
        return $timeline;
    }



    /**
     * @return Timeline
     */
    public function createComponentCareTimeline() : Timeline
    {
        $item1 = new Item();
        $item1->setReverse(FALSE);
        $item1->setContentClass(' u-pt--40 u-md-pt--70');
        $item1->setTitle($this->translator->translate('pageStatic.services.care.block1.title'));
        $item1->setSubtitle($this->translator->translate('pageStatic.services.care.block1.subtitle'));
        $item1->setDescription($this->translator->translate('pageStatic.services.care.block1.description'));
        $item1->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item1->setImages('<img src="/assets/front/images/layout/sperk-kontrola.jpg" alt="" class="Timeline-image-primary">');

        $item2 = new Item();
        $item2->setReverse(TRUE);
        $item2->setRowClass(' u-mt--30 u-md-mt--50');
        $item2->setContentClass(' u-pt--20 u-sm-pt--70');
        $item2->setSubtitle($this->translator->translate('pageStatic.services.care.block2.title'));
        $item2->setDescription($this->translator->translate('pageStatic.services.care.block2.description'));
        $item2->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item2->setImagesClass(' u-mt--0 u-mb--0');
        $item2->setImages('<img src="/assets/front/images/layout/sperk-lesteni.jpg" alt="" class="Timeline-image-primary">');

        $timeline = $this->timelineFactory->create();
        $timeline->addItem($item1);
        $timeline->addItem($item2);
        return $timeline;
    }



    /**
     * @return Timeline
     */
    public function createComponentRepairTimeline() : Timeline
    {
        $item1 = new Item();
        $item1->setReverse(FALSE);
        $item1->setContentClass(' u-pt--40 u-md-pt--70');
        $item1->setSubtitle($this->translator->translate('pageStatic.services.repair.title'));
        $item1->setDescription($this->translator->translate('pageStatic.services.repair.description'));
        $item1->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item1->setImages('<img src="/assets/front/images/layout/sperk-fasovani.jpg" alt="" class="Timeline-image-primary">');

        $item2 = new Item();
        $item2->setReverse(TRUE);
        $item2->setRowClass(' u-mt--30 u-md-mt--50');
        $item2->setContentClass(' u-pt--20 u-sm-pt--70');
        $item2->setSubtitle($this->translator->translate('pageStatic.services.repair.watch.title'));
        $item2->setDescription($this->translator->translate('pageStatic.services.repair.watch.description'));
        $item2->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item2->setImagesClass(' u-mt--0 u-mb--0');
        $item2->setImages('<img src="/assets/front/images/layout/hodinky-servis.jpg" alt="" class="Timeline-image-primary">');

        $timeline = $this->timelineFactory->create();
        $timeline->addItem($item1);
        $timeline->addItem($item2);
        return $timeline;
    }
}