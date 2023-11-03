<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\Showroom;

use App\FrontModule\Components\Page\Block\Timeline\Item;
use App\FrontModule\Components\Page\Block\Timeline\Timeline;
use App\FrontModule\Components\Page\Block\Timeline\TimelineFactory;
use Kdyby\Translation\ITranslator;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Showroom extends Control
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
    public function createComponentHistoryTimeline(): Timeline
    {
        $item1 = new Item();
        $item1->setReverse(TRUE);
        $item1->setContentClass(' u-pt--20 u-sm-pt--0 u-md-mb--40');
        $item1->setDescription($this->translator->translate('pageStatic.showroom.history.timeline1.description'));
        $item1->setImages('<img src="/assets/front/images/layout/zlatnicka-dilna-jak-vznika-sperk-2.jpg" alt="" class="Timeline-image-primary">
                           <img src="/assets/front/images/layout/budova-filadelfie.jpg" alt="" class="Timeline-image-secondary">');

        $item2 = new Item();
        $item2->setReverse(FALSE);
        $item2->setContentClass(' u-pt--20 u-md-pt--70');
        $item2->setDescription($this->translator->translate('pageStatic.showroom.history.timeline2.description'));
        $item2->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item2->setImages('<img src="/assets/front/user_content/images/klient-obsluha.jpg" alt="" class="Timeline-image-primary">');

        $item3 = new Item();
        $item3->setReverse(TRUE);
        $item3->setContentClass(' u-pt--20 u-md-pt--70');
        $item3->setDescription($this->translator->translate('pageStatic.showroom.history.timeline3.description'));
        $item3->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item3->setImages('<img src="/assets/front/images/layout/filadelfie-parkovani.jpg" alt="" class="Timeline-image-primary">');

        $timeline = $this->timelineFactory->create();
        $timeline->addItem($item1);
        $timeline->addItem($item2);
        $timeline->addItem($item3);
        return $timeline;
    }


    public function createComponentLoungeTimeline() : Timeline
    {
        $item1 = new Item();
        $item1->setReverse(FALSE);
        $item1->setTitle($this->translator->translate('pageStatic.showroom.lounge.timeline1.title'));
        $item1->setDescription($this->translator->translate('pageStatic.showroom.lounge.timeline1.description'));
        $item1->setImagesClass(' u-mt--60 u-sm-mt--ofset-2x160');
        $item1->setImages('<img src="/assets/front/images/layout/zlatnicka-dilna-ctime-zlatnicke-remeslo-2.jpg" alt="" class="Timeline-image-primary">
                           <img src="/assets/front/images/layout/zlatnicka-dilna-stoly.jpg" alt="" class="Timeline-image-secondary">
                           <span class="Timeline-image-ornament-1"></span>
                           <span class="Timeline-image-ornament-2"></span>');

        $item2 = new Item();
        $item2->setReverse(TRUE);
        $item2->setRowClass(' u-mt--30 u-md-mt--100');
        $item2->setContentClass(' u-pt--20 u-sm-pt--70');
        $item2->setDescription($this->translator->translate('pageStatic.showroom.lounge.timeline2.description'));
        $item2->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item2->setImagesClass(' u-mt--0 u-mb--0');
        $item2->setImages('<img src="/assets/front/images/layout/zlatnicka-dilna-ctime-zlatnicke-remeslo-3.jpg" alt="" class="Timeline-image-primary">');

        $timeline = $this->timelineFactory->create();
        $timeline->addItem($item1);
        $timeline->addItem($item2);
        return $timeline;
    }
}