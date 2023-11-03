<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;


use App\FrontModule\Components\Page\StaticPage\Meeting\Meeting;
use App\FrontModule\Components\Page\StaticPage\Meeting\MeetingFactory;
use App\FrontModule\Components\Breadcrumb\Item;
use App\FrontModule\Components\Breadcrumb\Navigation;



final class WeddingPresenter extends AbstractPresenter
{
    /** @var MeetingFactory @inject */
    public $meetingFactory;

    /** @var Navigation */
    public $breadcrumb;

		
    public function startup()
    {
        parent::startup();
				$this->breadcrumb = new Navigation();
		}
		
		
    public function actionDefault()
    {
        $this->breadcrumb->addItem(new Item($this->translator->translate('wedding.title'), $this->link('Wedding')));
			
        $this->template->title = $this->translator->translate('wedding.title');				
		}

		
   /**
     * @return MeetingForm
     */
    public function createComponentMeetingForm() : Meeting
    {
        return $this->meetingFactory->create();
    }

}