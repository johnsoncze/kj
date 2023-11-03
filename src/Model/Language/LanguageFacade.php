<?php

namespace App\Language;

use App\FacadeException;
use App\Helpers\Entities;
use App\NotFoundException;
use App\ServiceException;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class LanguageFacade extends NObject
{


    /** @var LanguageRepositoryFactory */
    protected $languageRepositoryFactory;

    /** @var LanguageActiveServiceFactory */
    protected $languageActiveServiceFactory;



    public function __construct(LanguageActiveServiceFactory $languageActiveServiceFactory,
                                LanguageRepositoryFactory $languageRepositoryFactory)
    {
        $this->languageActiveServiceFactory = $languageActiveServiceFactory;
        $this->languageRepositoryFactory = $languageRepositoryFactory;
    }



    /**
     * @param $id int
     * @return LanguageEntity
     * @throws FacadeException
     */
    public function active($id)
    {
        try {
            $repo = $this->languageRepositoryFactory->create();
            $language = $repo->getOneById($id);
            $this->languageActiveServiceFactory->create()->setActive($language);
            $repo->save($language);
            return $language;
        } catch (NotFoundException $exception) {
            throw new FacadeException($exception->getMessage());
        }
    }



    /**
     * @param $id int
     * @return LanguageEntity
     * @throws FacadeException
     */
    public function deactive($id)
    {
        try {
            $repo = $this->languageRepositoryFactory->create();
            $language = $repo->getOneById($id);
            $this->languageActiveServiceFactory->create()->setDeactive($language);
            $repo->save($language);
            return $language;
        } catch (NotFoundException $exception) {
            throw new FacadeException($exception->getMessage());
        } catch (ServiceException $exception) {
            throw new FacadeException($exception->getMessage());
        }
    }


}