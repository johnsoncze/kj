<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\ProductionTimeForm;

use App\Components\BaseFormContainer;
use App\Product\Production\ProductionTimeDTO;
use App\Product\Production\ProductionTrait;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class FormContainer extends BaseFormContainer
{


    /** @var string */
    const NAME = 'productionTimeForm';



    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $timeList = [];
        $times = ProductionTrait::getProductionTimes();
        foreach ($times as $time) {
            $text = $this->translator->translate($time->getTranslationKey());
            $text .= $time->hasSurcharge() ? sprintf(' (+ %s %%)', $time->getSurchargePercent()) : NULL;
            $timeList[$time->getKey()] = $text;
        }
        $this->addSelect('productionTime', $this->translator->translate('form.productionTime.label'), $timeList)
            ->setDefaultValue(ProductionTimeDTO::PRODUCTION_4_6_WEEKS);
    }



    /**
     * @inheritdoc
     */
    public function render()
    {
        $template = $this->getTemplate();
        $template->name = self::NAME;
        $template->parentForm = $this->getParent();
        $template->render(__DIR__ . "/default.latte");
    }

}