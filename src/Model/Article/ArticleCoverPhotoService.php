<?php

namespace App\Article;

use App\Libs\FileManager\Exceptions\UploaderException;
use App\Libs\FileManager\FileManager;
use Nette\Http\FileUpload;
use App\NObject;
use Nette\Utils\Random;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCoverPhotoService extends NObject
{


    /** @var string */
    const FOLDER = "article-cover-photos";

    /** @var FileManager */
    protected $fileManager;



    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }



    /**
     * @param $fileUpload FileUpload
     * @return string
     * @throws ArticleCoverPhotoServiceException
     */
    public function upload(FileUpload $fileUpload)
    {
        try {
            $this->fileManager->setFolder(self::FOLDER);
            $name = $this->fileManager->upload($fileUpload, Random::generate(32));
            $this->fileManager->flush();
            return $name;
        } catch (UploaderException $exception) {
            throw new ArticleCoverPhotoServiceException($exception->getMessage());
        }
    }

}

class ArticleCoverPhotoServiceException extends \Exception
{


}