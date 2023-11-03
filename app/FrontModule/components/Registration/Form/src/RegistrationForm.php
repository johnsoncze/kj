<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Registration\Form;

use App\Customer\Customer;
use App\Customer\CustomerStorageException;
use App\Customer\CustomerStorageFacadeFactory;
use App\Environment\Environment;
use App\FrontModule\Components\BirthdayFormContainer\BirthdayFormContainer;
use App\FrontModule\Components\FormSpamProtection;
use App\Google\TagManager\DataLayer;
use Kdyby\Monolog\Logger;
use Kdyby\Translation\ITranslator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Nette\InvalidStateException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class RegistrationForm extends AbstractRegistrationForm
{


    use FormSpamProtection;

    /** @var string */
    const LOGGER_NAMESPACE = 'registration.form';

    /** @var CustomerStorageFacadeFactory */
    private $customerStorageFacadeFactory;

    /** @var Logger */
    private $logger;

    /** @var DataLayer */
    private $gtmDataLayer;




    public function __construct(BirthdayFormContainer $birthdayFormContainer,
                                Context $context,DataLayer $dataLayer,
                                CustomerStorageFacadeFactory $customerStorageFacadeFactory,
                                ITranslator $translator,
                                Logger $logger)
    {
        parent::__construct($birthdayFormContainer, $context, $translator);
        $this->customerStorageFacadeFactory = $customerStorageFacadeFactory;
        $this->logger = $logger;
        $this->gtmDataLayer = $dataLayer;
    }



    /**
     * @return Form
     * @throws InvalidStateException
     */
    public function createComponentForm() : Form
    {
        $form = parent::createComponentForm();
        $this->addSpamProtection($form);

        //password
        $password = $form->addPassword('password', $this->translator->translate('form.registration.label.password') . '*', NULL, 50)
            ->setRequired($this->translator->translate('form.registration.error.password'));
        $form->addPassword('confirmPassword', $this->translator->translate('form.registration.label.confirmPassword') . '*', NULL, 50)
            ->setRequired($this->translator->translate('form.registration.error.confirmPassword'))
            ->addConditionOn($password, Form::FILLED, TRUE)
            ->addRule(Form::EQUAL, $this->translator->translate('form.registration.error.passwordsAreNotEqual'), $password);

        //hear about us
        $form->addSelect('hearAboutUs', $this->translator->translate('form.hearAboutUs.label.general'), Customer::getHearAboutUsList($this->translator))
            ->setPrompt($this->translator->translate('form.general.selectbox.prompt'))
            ->setAttribute('class', 'js-selectfield');
        $form->addTextArea('hearAboutUsComment', $this->translator->translate('form.hearAboutUs.label.comment'))
            ->setMaxLength(255);

        //addition information
        $form->addCheckbox('personalData', $this->translator->translate('form.registration.label.personalData'))
            ->setRequired($this->translator->translate('form.registration.error.personalData'));

        $contactCheckbox = $form->getComponent('contact');
        $this->birthdayForm['month']->addConditionOn($contactCheckbox, Form::FILLED, TRUE)
            ->setRequired($this->translator->translate('form.birthday.error.month'));

        $form->addSubmit('submit', $this->translator->translate('form.registration.label.submit'));

        return $form;
    }



    /**
     * Handler for success sent form.
     * @param $form Form
     * @return void
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $values->postCode = str_replace(' ', '', $values->postCode);
        $presenter = $this->getPresenter();
        $environment = Environment::create();
        $this->processSpamRequest($values, $presenter, $this->translator);

        try {
            $this->database->beginTransaction();
            $customerStorage = $this->customerStorageFacadeFactory->create();
            $customer = $customerStorage->addFromRegistrationForm($values->name, $values->surname, $values->email, $values->password, $values->sex, (int)$values->{BirthdayFormContainer::NAME}->year ?: NULL,
                (int)$values->{BirthdayFormContainer::NAME}->month ?: NULL, (int)$values->{BirthdayFormContainer::NAME}->day ?: NULL, $values->street ?: NULL,
                $values->city ?: NULL, (int)$values->postCode ?: NULL, (string)$values->state ?: NULL, $values->telephone ?: NULL, $values->hearAboutUs ? (string)$values->hearAboutUs : NULL,
                \App\Helpers\Strings::mb4tomb3($values->hearAboutUsComment) ?: null, true);
            $this->database->commit();

            //add data for google tag manager
            $this->gtmDataLayer->add([
                'formular' => 'Registrace zákazníka',
                'event' => 'formSent',
            ]);

            //log
            $this->logger->addInfo(sprintf(self::LOGGER_NAMESPACE . ': Registrace zákazníka s id \'%d\'.', $customer->getId()), ['environment' => $environment->getType(), 'route' => $presenter->getAction(TRUE)]);

            $presenter->flashMessage($this->translator->translate('form.registration.message.success'), 'success');
            $presenter->redirect('Sign:in');
        } catch (CustomerStorageException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . '/templates/default.latte');
        $this->template->render();
    }
}
