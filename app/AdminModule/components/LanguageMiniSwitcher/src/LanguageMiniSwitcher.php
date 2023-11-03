<?php

declare(strict_types = 1);

namespace App\Components\LanguageMiniSwitcher;

use App\Language\LanguageRepositoryFactory;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class LanguageMiniSwitcher extends Control
{


    /** @var LanguageRepositoryFactory */
    protected $languageRepositoryFactory;



    public function __construct(LanguageRepositoryFactory $languageRepositoryFactory)
    {
        $this->languageRepositoryFactory = $languageRepositoryFactory;
    }



    /**
     * Get actual language id
     * @return mixed
     */
    public function getLangId()
    {
        $default = $this->presenter->context->getParameters()['administration']['language']['default'];
        return $this->getParameter("langId", $default);
    }



    public function render()
    {
        $languageRepository = $this->languageRepositoryFactory->create();
        $languages = $languageRepository->findAll();

        $this->template->languages = $languages;
        $this->template->actualLanguage = $this->getLangId();
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }
}