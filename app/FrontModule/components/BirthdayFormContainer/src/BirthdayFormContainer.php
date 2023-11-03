<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\BirthdayFormContainer;

use App\Components\BaseFormContainer;
use App\Customer\Customer;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class BirthdayFormContainer extends BaseFormContainer
{


    /** @var string */
    const NAME = 'birthdayForm';



    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->addSelect('day', NULL, Customer::getMonthDayList())
            ->setPrompt($this->translator->translate('form.birthday.label.day'))
            ->setAttribute('class', 'js-selectfield');
        $this->addSelect('month', NULL, Customer::getMonthList($this->translator))
            ->setPrompt($this->translator->translate('form.birthday.label.month'))
            ->setAttribute('class', 'js-selectfield');
        $this->addSelect('year', NULL, $this->getYearList())
            ->setPrompt($this->translator->translate('form.birthday.label.year'))
            ->setAttribute('class', 'js-selectfield');
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
     * Get year list.
     * @return array
     */
    protected function getYearList() : array
    {
        $years = [];
        $actualYear = date('Y');
        $from = $actualYear - 120;
        $to = $actualYear - 15;
        for ($i = $to; $i >= $from; $i--) {
            $years[$i] = $i;
        }
        return $years;
    }

}