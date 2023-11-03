<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use Nette\Application\BadRequestException;
use Nette\Application\Responses\TextResponse;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ExceptionPresenter extends AdminModulePresenter
{


    /**
     * Show an exception.
     * @param $name string
     * @return void
     * @throws BadRequestException
     */
    public function actionShow(string $name)
    {
        $file = $this->getExceptionPath($name);
        $content = file_get_contents($file);
        $this->sendResponse(new TextResponse($content));
    }



    /**
     * Delete an exception.
     * @param $name string
     * @return void
     * @throws BadRequestException
     */
    public function actionDelete(string $name)
    {
        $file = $this->getExceptionPath($name);
        unlink($file);
        $this->sendResponse(new TextResponse('Ok.'));
    }



    /**
     * Get absolute path for an exception.
     * @param $fileName string
     * @return string
     * @throws BadRequestException
     */
    private function getExceptionPath(string $fileName) : string
    {
        $logFolder = $this->context->getParameters()['logDir'];
        $file = $logFolder . DIRECTORY_SEPARATOR . $fileName;
        if (!is_file($file)) {
            throw new BadRequestException(NULL, 404);
        }
        return $file;
    }
}