<?php

namespace Ricaefeliz\Mappero\Translation;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class LocalizationResolver extends NObject
{


    /** @var TranslationConfig|null */
    protected static $config;



    /**
     * @param string $prefix
     * @return int
     * @throws LocalizationResolverException
     */
    public function getId(string $prefix) : int
    {
        $config = self::getConfig();
        $whiteList = $config->getWhiteList();
        if (!isset($whiteList[$prefix])) {
            throw new LocalizationResolverException("Unknown language '$prefix'.");
        }
        return $whiteList[$prefix];
    }



    /**
     * @param string $prefix
     * @return Localization
     * @throws LocalizationResolverException
     */
    public function getByPrefix(string $prefix) : Localization
    {
        $config = self::getConfig();
        $list = $config->getWhiteList();
        $id = $list[$prefix] ?? NULL;
        if ($id === NULL) {
            throw new LocalizationResolverException("Missing language for prefix '$prefix'.");
        }
        return new Localization($id, $prefix);
    }



    /**
     * @param int $id
     * @return Localization
     * @throws LocalizationResolverException
     */
    public function getById(int $id) : Localization
    {
        $config = self::getConfig();
        $list = $config->getWhiteList();
        $prefix = array_search($id, $list);
        if ($prefix === FALSE) {
            throw new LocalizationResolverException("Missing language wih id '$id'.");
        }
        return new Localization($id, $prefix);
    }



    /**
     * @return Localization
     */
    public function getActual() : Localization
    {
        return new Localization(1, 'cs');
        $config = self::getConfig();
        $actual = $config->getActual();
        $list = $config->getWhiteList();
        return new Localization($list[$actual], $actual);
    }



    /**
     * @return Localization
     */
    public function getDefault() : Localization
    {
        $config = self::getConfig();
        $default = $config->getDefault();
        $list = $config->getWhiteList();
        return new Localization($list[$default], $default);
    }



    /**
     * @param TranslationConfig $translationConfig
     */
    public static function setConfig(TranslationConfig $translationConfig)
    {
        self::$config = $translationConfig;
    }



    /**
     * @return TranslationConfig
     * @throws LocalizationResolverException
     */
    public static function getConfig() : TranslationConfig
    {
        if (!self::$config) {
            throw new LocalizationResolverException("You must set '" . TranslationConfig::class . "' as config.");
        }
        return self::$config;
    }
}


class LocalizationResolverException extends \Exception
{


}