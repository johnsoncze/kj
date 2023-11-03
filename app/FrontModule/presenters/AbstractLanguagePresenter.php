<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Language\LanguageDTO;
use App\Language\LanguageEntity;
use App\Language\LanguageRepository;
use App\NotFoundException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Nette\Utils\Strings;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class AbstractLanguagePresenter extends Presenter
{


    public $locale = 'cs';

    /** @var LanguageDTO|null */
    public $language;

    /** @var LanguageEntity|null */
    public $languageEntity;

    /** @var LanguageRepository @inject */
    public $languageRepo;


    public function startup()
    {
        parent::startup();

        $url = $this->getHttpRequest()->getUrl();
        $pathInfo = $url->getPathInfo();
        $parts = explode("/", $pathInfo);
        $lastPart = array_pop($parts);

        if ($lastPart != Strings::webalize($lastPart, '.')) {
            $parts[] = Strings::webalize($lastPart, '.');
            $newUrl = '/' . implode('/', $parts);
            $this->redirectUrl($newUrl);
        }

        try {
            $this->languageEntity = $this->locale ? $this->languageRepo->getOneByPrefix($this->locale) : $this->languageRepo->getOneDefaultActive();
            $this->language = new LanguageDTO($this->languageEntity->getId(), $this->languageEntity->getPrefix());
        } catch (NotFoundException $exception) {
            throw new BadRequestException(null, 404);
        }
    }
}