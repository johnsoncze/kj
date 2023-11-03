<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\Team;

use App\FrontModule\Components\Page\Block\Timeline\Item;
use App\FrontModule\Components\Page\Block\Timeline\Timeline;
use App\FrontModule\Components\Page\Block\Timeline\TimelineFactory;
use Kdyby\Translation\ITranslator;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Team extends Control
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
    public function createComponentOwnerTimeline() : Timeline
    {
        $item1 = new Item();
        $item1->setReverse(FALSE);
        $item1->setContentClass(' u-pt--40 u-md-pt--70');
        $item1->setTitle('Jitka Mlynarčík Kudláčková');
        $item1->setSubtitle($this->translator->translate('pageStatic.team.jitka.position'));
        $item1->setDescription('<em>„' . $this->translator->translate('pageStatic.team.jitka.citation') . '"</em>');
        $item1->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item1->setImages('<img src="/assets/front/user_content/images/jitka-3.jpg" alt="" class="Timeline-image-primary">');

        $item2 = new Item();
        $item2->setReverse(TRUE);
        $item2->setRowClass(' u-mt--30 u-md-mt--50');
        $item2->setContentClass(' u-pt--20 u-sm-pt--70');
        $item2->setTitle('Dušan Mlynarčík');
        $item2->setSubtitle($this->translator->translate('pageStatic.team.dusan.position'));
        $item2->setDescription('<em>„' . $this->translator->translate('pageStatic.team.dusan.citation') . '"</em>');
        $item2->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item2->setImagesClass(' u-mt--0 u-mb--0');
        $item2->setImages('<img src="/assets/front/user_content/images/dusan-2.jpg" alt="" class="Timeline-image-primary">');

        $timeline = $this->timelineFactory->create();
        $timeline->addItem($item2);
        $timeline->addItem($item1);
        return $timeline;
    }
}