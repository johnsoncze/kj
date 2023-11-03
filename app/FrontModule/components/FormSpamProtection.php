<?php

declare(strict_types = 1);

namespace App\FrontModule\Components;

use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait FormSpamProtection
{


    /**
     * @param $form Form
     * @return Form
     */
    protected function addSpamProtection(Form $form) : Form
    {
        $form->addText('personFullName', 'Full name');
        $form->addText('companyFullName', 'Company email');
        $form->addText('gRecaptchaResponse', 'gRecaptchaResponse');

        return $form;
    }
    /**
     * @param $gRecaptchaRespons json
     * @return bool
     */
    protected function isCaptchaOk($RecaptchaResponse)
    {
        return true;
        $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Ld20NMUAAAAACpmp4D6MBLxBFNyMxEB1diUsBR3&response={$RecaptchaResponse}");
        $Return = json_decode($Response);
        if ($Return->success == true && $Return->score < 0.5) {
            return false;
        } else {
            return true;
        }
        return true;
    }

    /**
     * @param $values ArrayHash
     * @return bool
     */
    protected function isSpam(ArrayHash $values) : bool
    {
        return $values->personFullName || $values->companyFullName|| !$this->isCaptchaOk($values->gRecaptchaResponse);
    }



    /**
     * Process spam request if spam request is sent.
     *
     * @param $values ArrayHash
     * @param $presenter Presenter
     * @param $translator ITranslator
     * @return void
     * @throws AbortException
     */
    protected function processSpamRequest(ArrayHash $values, Presenter $presenter, ITranslator $translator)
    {
        if ($this->isSpam($values)) {
            $presenter->flashMessage($translator->translate('form.spam.message'), 'danger');
            $presenter->redirect('Homepage:default');
        }
    }
}
