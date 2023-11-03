<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Translation;

use Ricaefeliz\Mappero\Annotations\PropertyException;
use Ricaefeliz\Mappero\Exceptions\EntityException;
use Ricaefeliz\Mappero\Exceptions\TranslationMissingException;
use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait TranslationTrait
{


    /** @var array */
    protected $translationsSortByLanguageId = [];



    /**
     * @param ITranslation $translation
     * @return $this
     */
    public function addTranslation(ITranslation $translation)
    {
        $this->translationsSortByLanguageId[$translation->getLanguageId()] = $translation;
        return $this;
    }



    /**
     * @param string|NULL $prefix
     * @return mixed
     * @throws TranslationMissingException
     */
    public function getTranslation(string $prefix = NULL) : ITranslation
    {
        try {
            $resolver = new LocalizationResolver();
            $localization = $prefix ? $resolver->getByPrefix($prefix) : $resolver->getActual();
            return $this->getTranslationById($localization->getId());
        } catch (LocalizationResolverException $exception) {
            throw new TranslationMissingException([
                TranslationMissingException::ENTITY_ID => $this->id ?: NULL,
                TranslationMissingException::ENTITY_NAME => get_called_class(),
                TranslationMissingException::LANGUAGE => $prefix
            ]);
        }
    }



    /**
     * @param $id int
     * @return ITranslation
     * @throws TranslationMissingException
     */
    public function getTranslationById(int $id) : ITranslation
    {
        if (!isset($this->translationsSortByLanguageId[$id])) {
            throw new TranslationMissingException([
                TranslationMissingException::ENTITY_ID => $this->id ?: NULL,
                TranslationMissingException::ENTITY_NAME => get_called_class(),
                TranslationMissingException::LANGUAGE => $id
            ]);
        }
        return $this->translationsSortByLanguageId[$id];
    }



    /**
     * @param $translations ITranslation[]
     * @return self
     * @throws EntityException
     * @throws PropertyException
     */
    public function setTranslations(array $translations)
    {
        Entities::hasProperty($this, 'translations');
        foreach ($translations as $translation) {
            if (!$translation instanceof ITranslation) {
                throw new EntityException(sprintf('Object must be instance of %s.', ITranslation::class));
            }
        }
        $this->translations = $translations;
        return $this;
    }



    /**
     * @return ITranslation[]|null
     */
    public function getTranslations()
    {
        Entities::hasProperty($this, 'translations');
        return $this->translations;
    }
}