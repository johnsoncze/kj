<?php

declare(strict_types = 1);

namespace App\Extensions\Nette;

use Nette\Http\IResponse;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class JsonResponse extends \Nette\Application\Responses\JsonResponse
{


	/** @var int */
	protected $code;



	public function __construct($payload, $contentType = NULL, int $code = IResponse::S200_OK)
	{
		parent::__construct($payload, $contentType);
		$this->code = $code;
	}



	/**
	 * @inheritdoc
	 */
	public function send(\Nette\Http\IRequest $httpRequest, \Nette\Http\IResponse $httpResponse)
	{
		$httpResponse->setContentType($this->getContentType(), 'utf-8');
		$httpResponse->setCode($this->code);
		echo \Nette\Utils\Json::encode($this->getPayload());
	}
}