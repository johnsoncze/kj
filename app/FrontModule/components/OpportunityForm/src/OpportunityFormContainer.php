<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\OpportunityForm;

use App\Components\BaseFormContainer;
use App\Customer\Customer;
use App\Opportunity\Opportunity;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\Checkbox;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OpportunityFormContainer extends BaseFormContainer
{


    /** @var string */
    const NAME = 'opportunityForm';



    /**
	 * @return void
    */
	public function applyCustomer()
	{
		/** @var $personalData Checkbox */
		$personalData = $this->getComponent('personalData');
		$personalData->setDefaultValue(TRUE);
	}



    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->addText('firstName', $this->translator->translate('form.opportunity.input.firstName.label') . '*')
            ->setRequired($this->translator->translate('form.opportunity.input.firstName.require'))
            ->setMaxLength(50);
        $this->addText('lastName', $this->translator->translate('form.opportunity.input.lastName.label') . '*')
            ->setRequired($this->translator->translate('form.opportunity.input.lastName.require'))
            ->setMaxLength(50);
        $preferredContact = $this->addRadioList('preferredContact', $this->translator->translate('form.opportunity.input.preferredContact.label'), Opportunity::getTranslatedPreferredContactList($this->translator))
            ->setDefaultValue(Opportunity::PREFERRED_CONTACT_TELEPHONE);
        $this->addText('email', 'E-mail')
            ->setMaxLength(50)
            ->addCondition(Form::FILLED)
            ->addRule(Form::EMAIL, $this->translator->translate('general.error.invalidEmailFormat'), TRUE)
            ->endCondition()
            ->addConditionOn($preferredContact, Form::EQUAL, Opportunity::PREFERRED_CONTACT_EMAIL)
            ->setRequired($this->translator->translate('form.opportunity.input.email.require'));
        $this->addText('telephone', $this->translator->translate('form.opportunity.input.telephone.label'))
            ->setMaxLength(20)
            ->addConditionOn($preferredContact, Form::EQUAL, Opportunity::PREFERRED_CONTACT_TELEPHONE)
            ->setRequired($this->translator->translate('form.opportunity.input.telephone.require'));
        $this->addText('requestDate', $this->translator->translate('form.opportunity.input.requestDate.label'))
            ->setMaxLength(100);
        $this->addTextArea('comment', $this->translator->translate('form.opportunity.input.comment.label'), NULL, 4)
            ->setMaxLength(Opportunity::MAX_LENGTH_COMMENT);
		$this->addCheckbox('personalData', $this->translator->translate('form.registration.label.personalData'))
			->setRequired($this->translator->translate('form.registration.error.personalData'));
    }



    /**
     * @inheritdoc
     */
    public function render()
    {
        $template = $this->getTemplate();
        $template->name = self::NAME;
        $template->parentForm = $this->getParent();
        $template->render(__DIR__ . '/Templates/formContainer.latte');
    }



    /**
     * @param $data Data
     * @param $erase bool
     * @return Container
    */
    public function setDefaultsFromData(Data $data, $erase = FALSE)
    {
        $values = [
            'firstName' => $data->getFirstName(),
            'lastName' => $data->getLastName(),
            'email' => $data->getEmail(),
            'telephone' => $data->getTelephone(),
            'comment' => $data->getComment(),
        ];
        return $this->setDefaults($values, $erase);
    }
}