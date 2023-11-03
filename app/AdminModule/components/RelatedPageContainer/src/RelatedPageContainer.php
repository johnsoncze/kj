<?php

namespace App\Components\RelatedPageContainer;

use App\Components\BaseFormContainer;


class RelatedPageContainer extends BaseFormContainer
{

    /** @var string */
    const NAME = "relatedPage";

    protected function configure()
    {
        $this->addTextArea("relatedPageText", "Základní text")
            ->setAttribute("class", "form-control")
            ->setAttribute("maxlength", "255")
            ->setNullable(true);

        $this->addText("relatedPageScrolledText", "Odscrollovaný text")
            ->setAttribute("class", "form-control")
            ->setAttribute("maxlength", "255")
            ->setNullable(true);
				
        $this->addText("relatedPageLink", "Odkaz")
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