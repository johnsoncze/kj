<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use App\Extensions\Nette\JsonResponse;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Http\FileUpload;
use Nette\Http\IResponse;
use Nette\InvalidStateException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CkEditorPresenter extends AdminModulePresenter
{


	/** @var array */
	private $config = [];



	/**
	 * @inheritdoc
	 * @throws AbortException
	 */
	public function startup()
	{
		parent::startup();

		try {
			$this->config = $this->getConfig();
		} catch (\InvalidArgumentException $exception) {
			$this->logger->addError($exception->getMessage());
			$this->sendResponse($this->createErrorResponse($exception->getMessage()));
		}
	}



	/**
	 * @throws BadRequestException
	 * @throws AbortException
	 */
	public function actionUpload()
	{
		$fileUpload = $this->getFileUpload();
		$fileName = sprintf('%s-%s', time(), $fileUpload->getSanitizedName());
		$filePath = $this->createFilePath($fileName);

		try {
			$fileUpload->move($filePath);
			$fileUrl = $this->createFileUrl($fileName);
			$response = $this->createSuccessResponse($fileName, $fileUrl);
		} catch (InvalidStateException $exception) {
			$response = $this->createErrorResponse($exception->getMessage());
			$this->logger->addError($exception->getMessage(), [
				'filePath' => $filePath,
			]);
		}

		$this->sendResponse($response);
	}



	/**
	 * @return FileUpload
	 * @throws BadRequestException
	 */
	private function getFileUpload(): FileUpload
	{
		/** @var $files FileUpload[]|array */
		$files = $this->getRequest()->getFiles();
		if (!$files) {
			throw new BadRequestException(NULL, 404);
		}

		return end($files);
	}



	/**
	 * @param $fileName string
	 * @param $fileUrl string
	 * @return JsonResponse
	 */
	private function createSuccessResponse(string $fileName, string $fileUrl): JsonResponse
	{
		return new JsonResponse([
			'fileName' => $fileName,
			'uploaded' => 1,
			'url' => $fileUrl,
		]);
	}



	/**
	 * @param $message string
	 * @return JsonResponse
	 */
	private function createErrorResponse(string $message): JsonResponse
	{
		return new JsonResponse([
			'error' => [
				'message' => $message,
			],
		], NULL, IResponse::S500_INTERNAL_SERVER_ERROR);
	}



	/**
	 * @param $fileName string
	 * @return string
	 */
	private function createFileUrl(string $fileName): string
	{
		return $this->config['url'] . DIRECTORY_SEPARATOR . $fileName;
	}



	/**
	 * @param $fileName string
	 * @return string
	 */
	private function createFilePath(string $fileName): string
	{
		return $this->config['dir'] . DIRECTORY_SEPARATOR . $fileName;
	}



	/**
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	private function getConfig(): array
	{
		$paths = $this->context->getParameters()['upload']['editor'];
		if (!isset($paths['baseDir'], $paths['url'], $paths['dir'])) {
			throw new \InvalidArgumentException('Missing config.');
		}
		return $paths;
	}
}