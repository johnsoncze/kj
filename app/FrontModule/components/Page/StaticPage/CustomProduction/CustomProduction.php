<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\CustomProduction;

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
final class CustomProduction extends Control
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

}