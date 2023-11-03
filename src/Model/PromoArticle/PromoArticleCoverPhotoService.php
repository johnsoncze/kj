<?php

namespace App\PromoArticle;

use App\Libs\FileManager\Exceptions\UploaderException;
use App\Libs\FileManager\FileManager;
use Nette\Http\FileUpload;
use App\NObject;
use Nette\Utils\Random;



class PromoArticleCoverPhotoService extends NObject
{


    /** @var string */
    const FOLDER = "promo-article-photos";

    /** @var FileManager */
    protected $fileManager;



    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }



    /**
     * @param $fileUpload FileUpload
     * @return string
     * @throws PromoArticleCoverPhotoServiceException
     */
    public function upload(FileUpload $fileUpload)
    {
        try {
            $this->fileManager->setFolder(self::FOLDER);
            $name = $this->fileManager->upload($fileUpload, Random::generate(32));
            $this->fileManager->flush();
            return $name;
        } catch (UploaderException $exception) {
            throw new PromoArticleCoverPhotoServiceException($exception->getMessage());
        }
    }

}

class PromoArticleCoverPhotoServiceException extends \Exception
{


}