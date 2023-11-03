<?php

namespace App\PeriskopModule\Presenters;

use Nette;
use Tracy\ILogger;

class ErrorPresenter implements Nette\Application\IPresenter
{

    use Nette\SmartObject;

    /** @var ILogger */
    private $logger;


    public function __construct(ILogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Nette\Application\Request $request
     * @return Nette\Application\IResponse|void
     */
    public function run(Nette\Application\Request $request)
    {
        $exception = $request->getParameter('exception');
        $this->logger->log($exception, ILogger::EXCEPTION);
        die($exception->getMessage());
    }
}
