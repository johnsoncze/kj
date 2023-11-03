<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\GoldsmithWorkshop;

use App\FrontModule\Components\OpportunityForm\OpportunityForm;
use App\FrontModule\Components\Page\Block\BannerSlim\BannerSlim;
use App\FrontModule\Components\Page\Block\BannerSlim\BannerSlimFactory;
use App\FrontModule\Components\Page\Block\Timeline\Item;
use App\FrontModule\Components\Page\Block\Timeline\Timeline;
use App\FrontModule\Components\Page\Block\Timeline\TimelineFactory;
use Kdyby\Translation\ITranslator;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class GoldsmithWorkshop extends Control
{


    /** @var BannerSlimFactory */
    private $bannerSlimFactory;

    /** @var TimelineFactory */
    private $timelineFactory;

    /** @var ITranslator */
    private $translator;



    public function __construct(BannerSlimFactory $bannerSlimFactory,
                                ITranslator $translator,
                                TimelineFactory $timelineFactory)
    {
        parent::__construct();
        $this->bannerSlimFactory = $bannerSlimFactory;
        $this->timelineFactory = $timelineFactory;
        $this->translator = $translator;
    }



    /**
     * @return Timeline
     */
    public function createComponentSoulTimeline(): Timeline
    {
        $item1 = new Item();
        $item1->setReverse(TRUE);
        $item1->setContentClass(' u-pt--20 u-sm-pt--0 u-md-mb--40');
        $item1->setTitle($this->translator->translate('pageStatic.goldsmithWorkshop.timeline1.title'));
        $item1->setSubtitle($this->translator->translate('pageStatic.goldsmithWorkshop.timeline1.subtitle'));
        $item1->setDescription($this->translator->translate('pageStatic.goldsmithWorkshop.timeline1.description'));
        $item1->setImages('<img src="/assets/front/images/layout/zlatnicka-dilna-jak-vznika-sperk-2.jpg" alt="" class="Timeline-image-primary">
                           <img src="/assets/front/user_content/images/page/design/0649.jpg" alt="" class="Timeline-image-secondary">');

        $item2 = new Item();
        $item2->setReverse(FALSE);
        $item2->setContentClass(' u-pt--20 u-md-pt--70');
        $item2->setSubtitle($this->translator->translate('pageStatic.goldsmithWorkshop.timeline2.subtitle'));
        $item2->setDescription($this->translator->translate('pageStatic.goldsmithWorkshop.timeline2.description'));
        $item2->setHtml('<div class="u-sm-textLeft u-md-textRight u-mt--20 u-displayNone u-sm-displayBlock">
                            <img src="/assets/front/user_content/images/timeline-drahokamy.jpg" class="">
                         </div>');
        $item2->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item2->setImages('<img src="/assets/front/user_content/images/page/design/0602.jpg" alt="" class="Timeline-image-primary">');

        $timeline = $this->timelineFactory->create();
        $timeline->addItem($item1);
        $timeline->addItem($item2);
        return $timeline;
    }



    /**
     * @return Timeline
     */
    public function createComponentCraftTimeline(): Timeline
    {
        $item1 = new Item();
        $item1->setReverse(FALSE);
        $item1->setTitle($this->translator->translate('pageStatic.goldsmithWorkshop.timeline3.title'));
        $item1->setDescription($this->translator->translate('pageStatic.goldsmithWorkshop.timeline3.description'));
        $item1->setImagesClass(' u-mt--60 u-sm-mt--ofset-2x160');
        $item1->setImages('<img src="/assets/front/images/layout/zlatnicka-dilna-ctime-zlatnicke-remeslo-2.jpg" alt="" class="Timeline-image-primary">
                           <img src="/assets/front/user_content/images/page/design/1128.jpg" alt="" class="Timeline-image-secondary">
                           <span class="Timeline-image-ornament-1"></span>
                           <span class="Timeline-image-ornament-2"></span>');

        $item2 = new Item();
        $item2->setReverse(TRUE);
        $item2->setRowClass(' u-mt--30 u-md-mt--100');
        $item2->setContentClass(' u-pt--20 u-sm-pt--70');
        $item2->setDescription($this->translator->translate('pageStatic.goldsmithWorkshop.timeline4.description'));
        $item2->setHtml(sprintf('<p class="Timeline-desc">%s</p>
                                 <div class="u-mt--40 u-textCenter u-sm-textLeft">
                                    <a href="%s" class="Button">%s</a>
                                 </div>',
            $this->translator->translate('pageStatic.goldsmithWorkshop.timeline4.html.description'),
            $this->getPresenter()->link('Category:default', ['url' => 'kolekce-jk']),
            $this->translator->translate('pageStatic.goldsmithWorkshop.timeline4.html.cta')));
        $item2->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item2->setImagesClass(' u-mt--0 u-mb--0');
        $item2->setImages('<img src="/assets/front/images/layout/zlatnicka-dilna-ctime-zlatnicke-remeslo-3.jpg" alt="" class="Timeline-image-primary">');

        $timeline = $this->timelineFactory->create();
        $timeline->addItem($item1);
        $timeline->addItem($item2);
        return $timeline;
    }



    /**
     * @return Timeline
     */
    public function createComponentTechnologyTimeline(): Timeline
    {
        $item1 = new Item();
        $item1->setReverse(TRUE);
        $item1->setContentClass(' u-pt--20 u-sm-pt--0 u-sm-mb--20');
        $item1->setSubtitle($this->translator->translate('pageStatic.goldsmithWorkshop.technology.timeline1.subtitle'));
        $item1->setSubtitleClass(' u-mt--0');
        $item1->setDescription($this->translator->translate('pageStatic.goldsmithWorkshop.technology.timeline1.description'));
        $item1->setImages('<img src="/assets/front/user_content/images/page/design/1003.jpg" alt="" class="Timeline-image-primary">');

        $item2 = new Item();
        $item2->setReverse(FALSE);
        $item2->setContentClass(' u-pt--20 u-sm-pt--0');
        $item2->setDescription($this->translator->translate('pageStatic.goldsmithWorkshop.technology.timeline2.description'));
        $item2->setImages('<img src="/assets/front/user_content/images/page/design/1148.jpg" alt="" class="Timeline-image-primary">');

        $item3 = new Item();
        $item3->setReverse(TRUE);
        $item3->setContentClass(' u-pt--20 u-sm-pt--0');
        $item3->setSubtitleClass(' u-mt--0');
        $item3->setSubtitle($this->translator->translate('pageStatic.goldsmithWorkshop.technology.timeline3.subtitle'));
        $item3->setDescription($this->translator->translate('pageStatic.goldsmithWorkshop.technology.timeline3.description'));
        $item3->setImages('<img src="/assets/front/user_content/images/page/design/0966.jpg" alt="" class="Timeline-image-primary">');

        $item4 = new Item();
        $item4->setReverse(FALSE);
        $item4->setContentClass(' u-pt--20 u-sm-pt--0');
        $item4->setSubtitleClass(' u-mt--0');
        $item4->setSubtitle($this->translator->translate('pageStatic.goldsmithWorkshop.technology.timeline4.subtitle'));
        $item4->setDescription($this->translator->translate('pageStatic.goldsmithWorkshop.technology.timeline4.description'));
        $item4->setImages('<img src="/assets/front/images/layout/zlatnicka-dilna-techniky-4.jpg" alt="" class="Timeline-image-primary">');

        $timeline = $this->timelineFactory->create();
        $timeline->addItem($item1);
        $timeline->addItem($item2);
        $timeline->addItem($item3);
        $timeline->addItem($item4);
        return $timeline;
    }



    /**
     * @return BannerSlim
     */
    public function createComponentContact(): BannerSlim
    {
        $presenter = $this->getPresenter();
        $parameters = $presenter->context->getParameters();
        $callLink = str_replace(['+', ' '], ['00', ''], $parameters['project']['telephone']);

        $item = new \App\FrontModule\Components\Page\Block\BannerSlim\Item();
        $item->setBackgroundColor('#f6f4f4');
        $item->setTitle($this->translator->translate('pageStatic.goldsmithWorkshop.contact.title'));
        $item->setDescription($this->translator->translate('pageStatic.goldsmithWorkshop.contact.description'));
        $item->setFootText($this->translator->translate('pageStatic.goldsmithWorkshop.contact.foottext'));
        $item->setLinkCallAnchor($this->translator->translate('pageStatic.goldsmithWorkshop.contact.linkCallAnchor'));
        $item->setLinkCallUrl($callLink);
        $item->setLinkEmailAnchor($this->translator->translate('pageStatic.goldsmithWorkshop.contact.linkEmailAnchor'));
        $item->setLinkEmailUrl('#' . OpportunityForm::CONTACT_FORM_ID);
        $item->setBannerClass(' u-mt--140 u-lg-mt--140 u-mb--140 u-lg-mb--140');

        return $this->bannerSlimFactory->create($item);
    }
}