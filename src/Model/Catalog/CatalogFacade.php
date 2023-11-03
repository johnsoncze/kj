<?php

declare(strict_types = 1);

namespace App\Catalog;

use App\Helpers\Images;
use App\Libs\FileManager\FileManager;
use App\NotFoundException;
use Nette\Http\FileUpload;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CatalogFacade
{


    /** @var CatalogRepository */
    private $catalogRepo;

    /** @var FileManager */
    protected $fileManager;



    public function __construct(CatalogRepository $catalogRepo,
                                FileManager $fileManager)
    {
        $this->catalogRepo = $catalogRepo;
        $this->fileManager = $fileManager;
    }



    /**
     * @param $id int|null
     * @param $type string
     * @param $state string
     * @return Catalog
     * @throws CatalogFacadeException
     * todo test
     */
    public function save(int $id = NULL,
                         string $type,
                         string $state) : Catalog
    {
        try {
            $catalog = $id !== NULL ? $this->catalogRepo->getOneById($id) : new Catalog();
            $catalog->setType($type);
            $catalog->setState($state);
            $this->catalogRepo->save($catalog);
            return $catalog;
        } catch (NotFoundException $exception) {
            throw new CatalogFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $sorting array [catalogId => sort,..]
     * @return bool
     * @throws CatalogFacadeException
     * todo test
    */
    public function saveSorting(array $sorting)
    {
        $catalogId = array_keys($sorting);
        $catalogs = $this->catalogRepo->findByMoreId($catalogId);
        foreach ($sorting as $id => $sort) {
            $catalog = $catalogs[$id] ?? NULL;
            if ($catalog === NULL) {
               throw new CatalogFacadeException(sprintf('Chybí položka s id \'%d\'.', $id));
            }
            $catalog->setSort($sort);
            $this->catalogRepo->save($catalog);
        }
        return TRUE;
    }



    /**
     * @param $id int
     * @return bool
     * @throws CatalogFacadeException
     * todo test
     */
    public function delete(int $id) : bool
    {
        try {
            $catalog = $this->catalogRepo->getOneById($id);
            $this->catalogRepo->remove($catalog);
            return TRUE;
        } catch (NotFoundException $exception) {
            throw new CatalogFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $id int
     * @param $photo FileUpload
     * @return Catalog
     * @throws CatalogFacadeException
     * todo test
     */
    public function uploadPhoto(int $id, FileUpload $photo) : Catalog
    {
        if ($photo->isImage() !== TRUE) {
            throw new CatalogFacadeException(sprintf('Fotografie musí být ve formátu %s.', implode(',', Images::getMimeTypes())));
        }

        try {
            $catalog = $this->catalogRepo->getOneById($id);

            //upload photo
            $this->fileManager->setFolder(Catalog::getUploadFolder($catalog));
            $fullName = $this->fileManager->upload($photo);
            $this->fileManager->flush();
            $catalog->setPhoto($fullName);

            //save
            $this->catalogRepo->save($catalog);

            return $catalog;
        } catch (NotFoundException $exception) {
            throw new CatalogFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $id int id of catalog
     * @return Catalog
     * @throws CatalogFacadeException
     * todo test
     */
    public function deletePhoto(int $id) : Catalog
    {
        try {
            $catalog = $this->catalogRepo->getOneById($id);
            $catalog->deletePhoto();
            $this->catalogRepo->save($catalog);
            return $catalog;
        } catch (NotFoundException $exception) {
            throw new CatalogFacadeException($exception->getMessage());
        }
    }
}