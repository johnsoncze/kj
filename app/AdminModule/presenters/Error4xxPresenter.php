<?php

namespace App\AdminModule\Presenters;

use Nette;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Error4xxPresenter extends AdminModulePresenter
{


    public function startup()
    {
        parent::startup();
        if (!$this->getRequest()->isMethod(Nette\Application\Request::FORWARD)) {
            $this->error();
        }

        $this->setLayout(__DIR__ . '/../presenters/templates/Error/@layout.latte');
    }



    public function renderDefault(Nette\Application\BadRequestException $exception)
    {
        // load template 403.latte or 404.latte or ... 4xx.latte
        $file = __DIR__ . "/templates/Error/{$exception->getCode()}.latte";
        $this->template->setFile(is_file($file) ? $file : __DIR__ . '/templates/Error/4xx.latte');
    }

}
