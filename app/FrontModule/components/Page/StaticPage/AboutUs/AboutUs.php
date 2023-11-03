<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\AboutUs;

use App\FrontModule\Components\Page\Block\Timeline\Item;
use App\FrontModule\Components\Page\Block\Timeline\Timeline;
use App\FrontModule\Components\Page\Block\Timeline\TimelineFactory;
use App\FrontModule\Components\Page\Block\Timeline\Year\TimelineYear;
use App\FrontModule\Components\Page\Block\Timeline\Year\TimelineYearFactory;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class AboutUs extends Control
{


    /** @var TimelineFactory */
    private $timelineFactory;

    /** @var TimelineYearFactory */
    private $timelineYearFactory;

    /** @var Translator */
    private $translator;



    public function __construct(TimelineFactory $timelineFactory,
								TimelineYearFactory $timelineYearFactory,
                                Translator $translator)
    {
        parent::__construct();
        $this->timelineFactory = $timelineFactory;
        $this->timelineYearFactory = $timelineYearFactory;
        $this->translator = $translator;
    }



    /**
     * @return Timeline
     */
    public function createComponentOwnersTimeline(): Timeline
    {
        $item1 = new Item();
        $item1->setReverse(TRUE);
        $item1->setContentClass(' u-pt--20 u-sm-pt--0 u-md-mb--40');
        $item1->setTitle($this->translator->translate('pageStatic.aboutUs.owners.title'));
        $item1->setDescription('<em>„' . $this->translator->translate('pageStatic.aboutUs.owners.citation') . '"</em><br><br>' . $this->translator->translate('pageStatic.aboutUs.owners.description'));
        $item1->setImages('<img src="/assets/front/user_content/images/naradi-stul.jpg" alt="" class="Timeline-image-primary">');

        $item2 = new Item();
        $item2->setReverse(FALSE);
        $item2->setContentClass(' u-pt--20 u-md-pt--70');
        $item2->setDescription($this->translator->translate('pageStatic.aboutUs.owners.description2'));
        $item2->setImages('<img src="/assets/front/user_content/images/dilna-2.jpg" alt="" class="Timeline-image-primary">');

        $timeline = $this->timelineFactory->create();
        $timeline->addItem($item1);
        $timeline->addItem($item2);
        return $timeline;
    }



    /**
     * @return Timeline
    */
    public function createComponentTeamTimeline() : Timeline
    {
        $item1 = new Item();
        $item1->setReverse(FALSE);
        $item1->setTitle($this->translator->translate('pageStatic.aboutUs.team.title'));
        $item1->setDescription('<em>„' . $this->translator->translate('pageStatic.aboutUs.team.citation') . '“</em>');
        $item1->setImagesClass(' u-mt--60 u-sm-mt--ofset-2x160');
        $item1->setImages('<img src="/assets/front/images/layout/zlatnicka-dilna-ctime-zlatnicke-remeslo-2.jpg" alt="" class="Timeline-image-primary">
                           <img src="/assets/front/user_content/images/jitka-david.jpg" alt="" class="Timeline-image-secondary">
                           <span class="Timeline-image-ornament-1"></span>
                           <span class="Timeline-image-ornament-2"></span>');

        $item2 = new Item();
        $item2->setReverse(TRUE);
        $item2->setRowClass(' u-mt--30 u-md-mt--100');
        $item2->setContentClass(' u-pt--20 u-sm-pt--70');
        $item2->setHtml(sprintf('<p class="Timeline-desc">%s</p>
                                 <div class="u-mt--40 u-textCenter u-sm-textLeft">
                                    <a href="%s" class="Button">%s</a>
                                 </div>',
            $this->translator->translate('pageStatic.aboutUs.team.description'),
            $this->getPresenter()->link('Page:detail', ['url' => 'tym']),
            $this->translator->translate('pageStatic.aboutUs.team.anchor')));
        $item2->setItem2Class(' u-pt--30 u-sm-pt--0');
        $item2->setImagesClass(' u-mt--0 u-mb--0');
        $item2->setImages('<img src="/assets/front/images/layout/zlatnicka-dilna-ctime-zlatnicke-remeslo-3.jpg" alt="" class="Timeline-image-primary">');

        $timeline = $this->timelineFactory->create();
        $timeline->addItem($item1);
        $timeline->addItem($item2);
        return $timeline;
    }



    /**
	 * @return TimelineYear
    */
    public function createComponentTimelineYear() : TimelineYear
	{
		$timeline = $this->timelineYearFactory->create();

		//items
		$timeline->addItem(new \App\FrontModule\Components\Page\Block\Timeline\Year\Item(
			$this->translator->translate('pageStatic.aboutUs.timeline.1990.title'),
			$this->translator->translate('pageStatic.aboutUs.timeline.1990.year'),
			$this->translator->translate('pageStatic.aboutUs.timeline.1990.description'),
			'<img src="/assets/front/user_content/images/jitka-skola.jpg" alt="" class="Timeline-image-primary">',
			TRUE
		));

		$timeline->addItem(new \App\FrontModule\Components\Page\Block\Timeline\Year\Item(
			$this->translator->translate('pageStatic.aboutUs.timeline.1991.title'),
			$this->translator->translate('pageStatic.aboutUs.timeline.1991.year'),
			$this->translator->translate('pageStatic.aboutUs.timeline.1991.description'),
			'<img src="/assets/front/user_content/images/dusan-jitka.jpg" alt="" class="Timeline-image-primary">',
			FALSE
		));

		$timeline->addItem(new \App\FrontModule\Components\Page\Block\Timeline\Year\Item(
			$this->translator->translate('pageStatic.aboutUs.timeline.1992.title'),
			$this->translator->translate('pageStatic.aboutUs.timeline.1992.year'),
			$this->translator->translate('pageStatic.aboutUs.timeline.1992.description'),
			'<img src="/assets/front/user_content/images/jitka-venezuela.jpg" alt="" class="Timeline-image-primary">',
			TRUE
		));

		$timeline->addItem(new \App\FrontModule\Components\Page\Block\Timeline\Year\Item(
			$this->translator->translate('pageStatic.aboutUs.timeline.1996_1.title'),
			$this->translator->translate('pageStatic.aboutUs.timeline.1996_1.year'),
			$this->translator->translate('pageStatic.aboutUs.timeline.1996_1.description'),
			'<img src="/assets/front/user_content/images/jitka-u-stolu.jpg" alt="" class="Timeline-image-primary">',
			FALSE
		));

		$timeline->addItem(new \App\FrontModule\Components\Page\Block\Timeline\Year\Item(
			$this->translator->translate('pageStatic.aboutUs.timeline.1996_2.title'),
			$this->translator->translate('pageStatic.aboutUs.timeline.1996_2.year'),
			$this->translator->translate('pageStatic.aboutUs.timeline.1996_2.description'),
			'<img src="/assets/front/user_content/images/husitska-obchod.jpg" alt="" class="Timeline-image-primary">',
			TRUE
		));

		$timeline->addItem(new \App\FrontModule\Components\Page\Block\Timeline\Year\Item(
			$this->translator->translate('pageStatic.aboutUs.timeline.1998.title'),
			$this->translator->translate('pageStatic.aboutUs.timeline.1998.year'),
			$this->translator->translate('pageStatic.aboutUs.timeline.1998.description'),
			'<img src="/assets/front/user_content/images/seifertova-obchod.jpg" alt="" class="Timeline-image-primary">',
			FALSE
		));

		$timeline->addItem(new \App\FrontModule\Components\Page\Block\Timeline\Year\Item(
			$this->translator->translate('pageStatic.aboutUs.timeline.2009.title'),
			$this->translator->translate('pageStatic.aboutUs.timeline.2009.year'),
			$this->translator->translate('pageStatic.aboutUs.timeline.2009.description'),
			'<img src="/assets/front/user_content/images/jk-pf-2008.jpg" alt="" class="Timeline-image-primary">',
			TRUE
		));

		$timeline->addItem(new \App\FrontModule\Components\Page\Block\Timeline\Year\Item(
			$this->translator->translate('pageStatic.aboutUs.timeline.2010.title'),
			$this->translator->translate('pageStatic.aboutUs.timeline.2010.year'),
			$this->translator->translate('pageStatic.aboutUs.timeline.2010.description') . '<br><br>' .
			$this->translator->translate('pageStatic.aboutUs.timeline.2010.description2'),
			'<img src="/assets/front/user_content/images/budova-filadelfie.jpg" alt="" class="Timeline-image-primary">',
			FALSE
		));

		$timeline->addItem(new \App\FrontModule\Components\Page\Block\Timeline\Year\Item(
			$this->translator->translate('pageStatic.aboutUs.timeline.2014.title'),
			$this->translator->translate('pageStatic.aboutUs.timeline.2014.year'),
			$this->translator->translate('pageStatic.aboutUs.timeline.2014.description'),
			'<img src="/assets/front/user_content/images/viva-vision-souprava.jpg" alt="" class="Timeline-image-primary">',
			TRUE
		));

		$timeline->addItem((new \App\FrontModule\Components\Page\Block\Timeline\Year\Item(
			$this->translator->translate('pageStatic.aboutUs.timeline.today.title'),
			$this->translator->translate('pageStatic.aboutUs.timeline.today.year'),
			$this->translator->translate('pageStatic.aboutUs.timeline.today.description') . '<br><br>' .
			$this->translator->translate('pageStatic.aboutUs.timeline.today.description2') . '<br><br>' .
			$this->translator->translate('pageStatic.aboutUs.timeline.today.description3') . '<br><br>' .
			$this->translator->translate('pageStatic.aboutUs.timeline.today.description4') . '<br><br>' .
			$this->translator->translate('pageStatic.aboutUs.timeline.today.description5'),
			'<img src="/assets/front/user_content/images/majitele-vejce.jpg" alt="" class="Timeline-image-primary">',
			FALSE
		))->setTitleClass('u-pt--60'));

		return $timeline;
	}
}