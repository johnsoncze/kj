<?php

declare(strict_types=1);

namespace App\FrontModule\Components\ShoppingCart\ContactInformationForm\Container;

use App\Components\BaseFormContainer;
use App\Helpers\Regex;
use App\Location\State;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Rules;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ContactInformationContainer extends BaseFormContainer
{


    /** @var string */
    const NAME = 'contactInformationForm';


    /**
     * @param $requiredConditionOn BaseControl|null
     * @return self
     */
    public function setConfigure(BaseControl $requiredConditionOn = null)
    {
        $firstName = $this->addText('firstName', $this->translator->translate('form.opportunity.input.firstName.label') . '*')
            ->setMaxLength(50);
        $this->prepareForRule($firstName, $requiredConditionOn)->setRequired($this->translator->translate('form.opportunity.input.firstName.require'));

        $lastName = $this->addText('lastName', $this->translator->translate('form.opportunity.input.lastName.label') . '*')
            ->setMaxLength(50);
        $this->prepareForRule($lastName, $requiredConditionOn)->setRequired($this->translator->translate('form.opportunity.input.lastName.require'));

        $email = $this->addText('email', 'E-mail*')
            ->setMaxLength(50)
            ->setType('email')
            ->setRequired($this->translator->translate('form.opportunity.input.email.require'))
            ->setHtmlAttribute('data-invalid-msg', $this->translator->translate('general.error.invalidEmailFormat'))
            ->setHtmlAttribute('data-validate-pattern',Regex::EMAIL);

        $telephone = $this->addText('telephone', $this->translator->translate('form.opportunity.input.telephone.label') . '*')
            ->setMaxLength(20)
            ->setHtmlAttribute('data-validate-pattern',Regex::PHONE)
        ;
        if ($requiredConditionOn == null) {
            $this->prepareForRule($telephone, $requiredConditionOn)
                ->setRequired($this->translator->translate('form.opportunity.input.telephone.require'))
                ->addCondition(Form::FILLED)->addRule(Form::PATTERN, $this->translator->translate('general.error.invalidPhoneFormat'), Regex::PHONE)
            ;
        }
        $street = $this->addText('street', $this->translator->translate('form.contact.label.street') . '*')
            ->setMaxLength(90);
        $this->prepareForRule($street, $requiredConditionOn)->setRequired($this->translator->translate('form.contact.input.street.require'));

        $city = $this->addText('city', $this->translator->translate('form.contact.label.city') . '*')
            ->setMaxLength(35);
        $this->prepareForRule($city, $requiredConditionOn)->setRequired($this->translator->translate('form.contact.input.city.require'));

        $postCode = $this->addText('postCode', $this->translator->translate('form.contact.label.postCode') . '*')
            ->setMaxLength(6)
            ->setRequired(false)
            ->addRule(Form::PATTERN, $this->translator->translate('form.contact.error.postCode'), Regex::POSTCODE)
            ->setHtmlAttribute('data-invalid-msg', $this->translator->translate('form.contact.error.postCode'))
            ->setHtmlAttribute('data-validate-pattern',Regex::POSTCODE);
        $this->prepareForRule($postCode, $requiredConditionOn)
            ->setRequired($this->translator->translate('form.contact.input.postcode.require'));

        $this->addSelect('state', $this->translator->translate('form.contact.label.state'))
            ->setItems(State::getList($this->translator))
            ->setAttribute('class', 'Form-input js-selectfield');

        return $this;
    }


    /**
     * @return void
     */
    protected function configure()
    {
        //nothing..
        //call configure manually
    }


    /**
     * @inheritdoc
     */
    public function render()
    {
        $template = $this->getTemplate();
        $template->name = self::NAME;
        $template->parentForm = $this->getParent();
        $template->render(__DIR__ . '/default.latte');
    }


    /**
     * Prepare form control for add rule.
     * @param $control BaseControl
     * @param $requiredConditionOn BaseControl
     * @return Rules|BaseControl
     */
    protected function prepareForRule(BaseControl $control, BaseControl $requiredConditionOn = null)
    {
        return $requiredConditionOn ? $control->addConditionOn($requiredConditionOn, Form::FILLED, true) : $control;
    }

}
