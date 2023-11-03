<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\Meeting;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Connection;
use App\Facades\MailerFacade;


final class Meeting extends Control
{
    /** @var MailerFacade */
    protected $mailerFacadeCustomer;

    /** @var MailerFacade */
    protected $mailerFacadeAdmin;
		
		private $database;

	
	public function __construct(Connection $database,
															MailerFacade $mailerFacade)
   {
       parent::__construct();
			 
			 $this->database = $database;
			 $this->mailerFacadeCustomer = $mailerFacade;
			 $this->mailerFacadeAdmin = $mailerFacade;
   }



   /**
    * @return Form
   */
   public function createComponentForm(): Form
   {

        $form = new Form();
				$form->addText('name')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyplňte prosím toto pole.')
            ->setMaxLength(255);
				
        $form->addText('surname')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyplňte prosím toto pole.')
            ->setMaxLength(255);
				
        $form->addEmail('email')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyplňte prosím toto pole.')
            ->setMaxLength(255);				
				
        $form->addText('phone')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyplňte prosím toto pole.')
            ->setMaxLength(255);						
				
        $form->addText('preferredDate')
            ->setAttribute('class', 'form-control')
            ->setMaxLength(255);						
				
        $form->addTextArea('note')
            ->setAttribute('class', 'form-control');
								
        $form->addCheckbox('wantNewsletter');
				
        $form->addSubmit('submit')
            ->setAttribute('class', 'btn btn-success');

        $form->onSuccess[] = [$this, 'formSuccess'];				
				
        return $form;		 
   }
	 
	 
    /**
     * @return void
     */
    public function render()
    {
				$this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }	 
	 
	 
    /**
     * @param $form Form
     * @return void
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $presenter = $this->getPresenter();
        $parameters = $presenter->context->getParameters();

				$nl_consent = $values->wantNewsletter ? 1 : 0;

        try {
						$this->database->query('INSERT INTO personal_meeting ', [
							'pm_name' => $values->name,
							'pm_surname' => $values->surname,
							'pm_email' => $values->email,
							'pm_phone' => $values->phone,
							'pm_preffered_date' => $values->preferredDate,
							'pm_note' => $values->note,
							'pm_nl_consent' => $nl_consent,
						]);
						
						$this->mailerFacadeCustomer->addTo($values->email);
						$this->mailerFacadeCustomer->setSubject("Vyplnění formuláře na jk.cz");
						$this->mailerFacadeCustomer->setTemplate("PersonalMeetingRequest");
						$this->mailerFacadeCustomer->send();
						
						$this->mailerFacadeAdmin->addTo($parameters['project']['email']);
						$this->mailerFacadeAdmin->setSubject("Sjednání osobní schůzky");
						$this->mailerFacadeAdmin->setTemplate("PersonalMeetingRequestAdmin", ['meeting' => $values]);
						$this->mailerFacadeAdmin->send();

						
						$presenter->redirect("Page:detail", 'sjednani-osobni-schuzky-potvrzeni');
        } catch (SubscriberFacadeException $exception) {
            $presenter->flashMessage($exception->getMessage());
        }
    }
		
		
}