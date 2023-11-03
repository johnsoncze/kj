<?php

namespace App\Components\UrlFormContainer;

use App\Components\BaseFormContainer;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class UrlFormContainer extends BaseFormContainer
{


    /** @var string */
    const NAME = "urlForm";



    public function configure()
    {
        $this->addText("url", "Url (V případě nevyplnění bude doplněna automaticky)")
            ->setAttribute("class", "form-control");
    }



    public function render()
    {
        $template = $this->getTemplate();
        $template->name = self::NAME;
        $template->parentForm = $this->getParent();
        $template->render(__DIR__ . "/default.latte");
    }
}