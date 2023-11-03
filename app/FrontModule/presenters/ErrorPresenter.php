<?php

namespace App\FrontModule\Presenters;

use Nette;
use Nette\Application\Responses;
use Tracy\ILogger;


/**
 * General ErrorPresenter which processed all error requests and send them to specific Error presenters.
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ErrorPresenter implements Nette\Application\IPresenter
{

    /** @var string */
    const DEFAULT_MODULE = 'front';

    use Nette\SmartObject;

    /** @var ILogger */
    private $logger;



    public function __construct(ILogger $logger)
    {
        $this->logger = $logger;
    }



    /**
     * @param $request Nette\Application\Request
     * @return Responses\ForwardResponse|Responses\CallbackResponse
     */
    public function run(Nette\Application\Request $request)
    {
        $exception = $request->getParameter('exception');
        $module = ucfirst($this->resolveModule($request));

        if ($exception instanceof Nette\Application\BadRequestException) {
            return new Responses\ForwardResponse($request->setPresenterName($module . ':' . 'Error4xx'));
        }

        $this->logger->log($exception, ILogger::EXCEPTION);
        return new Responses\CallbackResponse(function () use ($module) {
            $locale = 'cs'; //todo set from request
            $moduleFolder = __DIR__ . '/../../' . $module . 'Module';
            require $moduleFolder . '/presenters/templates/Error/500.phtml';
        });
    }



    /**
	 * @param $request Nette\Application\Request
	 * @return string
    */
    private function resolveModule(Nette\Application\Request $request) : string
	{
		$errorRequest = $request->getParameter('request');
		if ($errorRequest) {
			list($module, , ,) = Nette\Application\Helpers::splitName($errorRequest->getPresenterName());
			$explodedModule = explode(':', $module); //because there can be more modules
			return $explodedModule[0];
		}

		return self::DEFAULT_MODULE;
	}

}
