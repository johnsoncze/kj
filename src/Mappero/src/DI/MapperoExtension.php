<?php

namespace Ricaefeliz\Mappero\DI;

use Nette\DI\CompilerExtension;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class MapperoExtension extends CompilerExtension
{


    /** @var string */
    const PREFIX = "mappero";

    /** @var string */
    const TAG_TRANSLATION_CONFIG = "translation";
    const TAG_TRANSLATION_MAPPING = "translation.mapping";

    /** @var array */
    public $defaults = [
        "default" => "cs",
        "whitelist" => [
            "cs" => 1
        ]
    ];



    public function loadConfiguration()
    {
        $config = $this->getConfig($this->defaults);

        $default = isset($config[self::TAG_TRANSLATION_CONFIG]["default"]) ? $config[self::TAG_TRANSLATION_CONFIG]["default"] : $this->defaults["default"];
        $list = isset($config[self::TAG_TRANSLATION_CONFIG]["whitelist"]) ? $config[self::TAG_TRANSLATION_CONFIG]["whitelist"] : $this->defaults["whitelist"];

        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix(self::PREFIX . "connection"))
            ->setClass('Ricaefeliz\Mappero\Bridges\NetteDatabase\NetteDatabase');

        $builder->addDefinition($this->prefix(self::PREFIX))
            ->setClass("Ricaefeliz\\Mappero\\Mappero");

        $builder->addDefinition($this->prefix("config"))
            ->setClass('Ricaefeliz\Mappero\Translation\TranslationConfig', [
                $list, $default
            ]);
    }



    public function afterCompile(\Nette\PhpGenerator\ClassType $class)
    {
        parent::afterCompile($class);

        $config = $this->getConfig();

        //Add actual language into Mappero
        $initialize = $class->getMethod('initialize');
        $initialize->addBody('
        
        $config = $this->getService("mappero.config");
        $config->setActual($this->getService("translation.default")->getLocale());
        Ricaefeliz\\Mappero\\Translation\\LocalizationResolver::setConfig($config);
        
        ');
    }
}