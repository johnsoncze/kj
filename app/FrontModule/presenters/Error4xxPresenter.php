<?php

namespace App\FrontModule\Presenters;

use Nette;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Error4xxPresenter extends AbstractPresenter
{


    /** @var string */
    const LOGGER_NAMESPACE = 'front.module.%s';



    public function startup()
    {
        parent::startup();

        //process only forwarded request
        if (!$this->getRequest()->isMethod(Nette\Application\Request::FORWARD) && !$this->getRequest()->isMethod('POST') &&  $this->getRequest()->getParameter('do')!="goToShoppingCart") {
            $this->error();
        }

        $this->template->index = FALSE;
    }



    /**
     * @param $exception Nette\Application\BadRequestException
     * @return void
     */
    public function renderDefault(Nette\Application\BadRequestException $exception)
    {
        $this->logBadRequest($exception);

        $file = __DIR__ . "/templates/Error/{$exception->getCode()}.latte";
        $this->template->setFile(is_file($file) ? $file : __DIR__ . '/templates/Error/4xx.latte');
        $this->template->title = $this->translator->translate('presenterFront.error.' . $exception->getCode() . '.title');
    }



    /**
     * Log bad request exception.
     * @param $exception Nette\Application\BadRequestException
     * @return Nette\Application\BadRequestException
     */
    private function logBadRequest(Nette\Application\BadRequestException $exception) : Nette\Application\BadRequestException
    {
        $namespace = sprintf(self::LOGGER_NAMESPACE, $exception->getCode());
        $message = sprintf('%s: %s', $namespace, $this->getHttpRequest()->getUrl()->getAbsoluteUrl());
        $this->logger->addNotice($message, ['badRequestException' => $exception]);
        return $exception;
    }

}
