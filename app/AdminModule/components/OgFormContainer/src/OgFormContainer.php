<?php

namespace App\Components\OgFormContainer;

use App\Components\BaseFormContainer;


class OgFormContainer extends BaseFormContainer
{

    /** @var string */
    const NAME = "ogForm";

    protected function configure()
    {
        $this->addText("titleOg", "Titulek")
            ->setAttribute("class", "form-control")
            ->setNullable(true);

        $this->addTextArea("descriptionOg", "Popis")
            ->setAttribute("class", "form-control")
            ->setAttribute("maxlength", "255")
            ->setNullable(true);
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