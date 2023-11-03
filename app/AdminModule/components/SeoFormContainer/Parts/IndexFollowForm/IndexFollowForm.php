<?php

declare(strict_types = 1);

namespace App\Components\SeoFormContainer\IndexFollowForm;

use App\Components\BaseFormContainer;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class IndexFollowForm extends BaseFormContainer
{


    /** @var string */
    const NAME = "indexFollowForm";



    protected function configure()
    {
        $this->addCheckbox("indexSeo", " Index")
            ->setDefaultValue(TRUE);
        $this->addCheckbox("followSeo", " Follow")
            ->setDefaultValue(TRUE);
    }



    /**
     * @return void
     */
    public function render()
    {
        $template = $this->getTemplate();
        $template->name = self::NAME;
        $template->parentForm = $this->getParent();
        $template->render(__DIR__ . "/default.latte");
    }
}