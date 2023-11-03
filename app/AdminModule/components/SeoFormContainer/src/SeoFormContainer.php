<?php

namespace App\Components\SeoFormContainer;

use App\Components\BaseFormContainer;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SeoFormContainer extends BaseFormContainer
{


    /** @var string */
    const NAME = "seoForm";



    protected function configure()
    {
        $this->addText("titleSeo", "Titulek")
            ->setAttribute("class", "form-control")
            ->setNullable(TRUE);
        $this->addTextArea("descriptionSeo", "Popis")
            ->setAttribute("class", "form-control")
            ->setAttribute("maxlength", "255")
            ->setNullable(TRUE);
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