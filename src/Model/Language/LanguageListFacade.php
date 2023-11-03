<?php

namespace App\Language;

use App\NObject;


class LanguageListFacade extends NObject
{


    /** @var LanguageRepositoryFactory */
    protected $languageRepositoryFactory;

    /** @var LanguageListServiceFactory */
    protected $languageListServiceFactory;



    public function __construct(LanguageRepositoryFactory $languageRepositoryFactory,
                                LanguageListServiceFactory $languageListServiceFactory)
    {
        $this->languageRepositoryFactory = $languageRepositoryFactory;
        $this->languageListServiceFactory = $languageListServiceFactory;
    }



    /**
     * @return array
     */
    public function getList()
    {
        $languages = $this->languageRepositoryFactory->create()->findBy([]);
        return $languages ? $this->languageListServiceFactory->create()->getList($languages) : [];
    }
}