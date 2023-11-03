<?php

declare(strict_types = 1);

namespace App\Catalog\Translation;

use App\Catalog\Catalog;
use App\Catalog\CatalogRepository;
use App\Libs\FileManager\FileManager;
use App\NotFoundException;
use Nette\Http\FileUpload;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CatalogTranslationFacade
{


    /** @var CatalogRepository */
    private $catalogRepo;

    /** @var CatalogTranslationRepository */
    private $catalogTranslationRepo;

    /** @var FileManager */
    private $fileManager;



    /**
     * CatalogTranslationFacade constructor.
     * @param CatalogRepository $catalogRepo
     * @param CatalogTranslationRepository $catalogTranslationRepo
     * @param FileManager $fileManager
     */
    public function __construct(CatalogRepository $catalogRepo,
                                CatalogTranslationRepository $catalogTranslationRepo,
                                FileManager $fileManager)
    {
        $this->catalogRepo = $catalogRepo;
        $this->catalogTranslationRepo = $catalogTranslationRepo;
        $this->fileManager = $fileManager;
    }



    /**
     * @param $id int|null
     * @param $catalogId int
     * @param $languageId int
     * @param $title string
     * @param $subtitle string|null
     * @param $text string|null
     * @param $about string|null
     * @return CatalogTranslation
     * @throws CatalogTranslationFacadeException
     * todo test
     */
    public function save(int $id = NULL,
                         int $catalogId,
                         int $languageId,
                         string $title,
                         string $subtitle = NULL,
                         string $text = NULL,
                         string $about = NULL
    ) : CatalogTranslation
    {
        try {
            $catalog = $this->catalogRepo->getOneById($catalogId);
            $catalogTranslation = $id !== NULL ? $this->catalogTranslationRepo->getOneById($id) : new CatalogTranslation();
            $catalogTranslation->setCatalogId($catalog->getId());
            $catalogTranslation->getId() === NULL ? $catalogTranslation->setLanguageId($languageId) : NULL;
            $catalogTranslation->setTitle($title);
            $catalogTranslation->setSubtitle($subtitle);
            $catalogTranslation->setText($text);
            $catalogTranslation->setAbout($about);
            $this->catalogTranslationRepo->save($catalogTranslation);

            return $catalogTranslation;
        } catch (NotFoundException $exception) {
            throw new CatalogTranslationFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $id int
     * @param $file FileUpload
     * @return CatalogTranslation
     * @throws CatalogTranslationFacadeException
     * todo test
     */
    public function uploadFile(int $id, FileUpload $file) : CatalogTranslation
    {
        try {
            $catalogTranslation = $this->catalogTranslationRepo->getOneById($id);
            $catalog = $this->catalogRepo->getOneById($catalogTranslation->getCatalogId());
            $this->fileManager->setFolder(Catalog::getUploadFolder($catalog));
            $fullName = $this->fileManager->upload($file);
            $this->fileManager->flush();
            $catalogTranslation->setFile($fullName);
            $catalogTranslation->setFileSize($file->getSize());
            $this->catalogTranslationRepo->save($catalogTranslation);

            return $catalogTranslation;
        } catch (NotFoundException $exception) {
            throw new CatalogTranslationFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $id int id of catalog translation
     * @return CatalogTranslation
     * @throws CatalogTranslationFacadeException
     */
    public function deleteFile(int $id) : CatalogTranslation
    {
        try {
            $catalogTranslation = $this->catalogTranslationRepo->getOneById($id);
            $catalogTranslation->deleteFile();
            $this->catalogTranslationRepo->save($catalogTranslation);
            return $catalogTranslation;
        } catch (NotFoundException $exception) {
            throw new CatalogTranslationFacadeException($exception->getMessage());
        }
    }
}