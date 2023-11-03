<?php

declare(strict_types = 1);

namespace App\Environment;

use Nette\Application\UI\Presenter;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Environment
{


    /** @var string */
    const LOCAL = 'local';
    const STAGING = 'staging';
    const PRODUCTION = 'production';

    /** @var string */
    protected $type;

    /** @var array */
    protected static $ip = [
        self::LOCAL => [
            'DESKTOP-2GHARO6', // mlezitom laptop CLI
            'HALL9000N', // vlada PC 1
            '::1',
        ],
        self::STAGING => [
           'playground.techtailors.cz',
            '89.187.150.125',
        ],
        self::PRODUCTION => [
            'vpsfc108018',
            '81.95.108.18',
            '104.26.8.225',
            '185.115.1.15',
            'devels.vshosting.cz',
        ],
    ];



    public function __construct(string $type)
    {
        $this->type = $type;
    }



    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }



    /**
     * @return bool
     */
    public function isProduction() : bool
    {
        return $this->getType() === self::PRODUCTION;
    }



    /**
     * @return bool
     */
    public function isStaging() : bool
    {
        return $this->getType() === self::STAGING;
    }



    /**
     * @return bool
     */
    public function isLocal() : bool
    {
        return $this->getType() === self::LOCAL;
    }



    /**
     * @param $presenter Presenter
     * @return bool
     */
    public function showMeasuringCodes(Presenter $presenter) : bool
    {
        return $presenter->isAjax() === FALSE && $this->isLocal() !== TRUE;
    }



    /**
     * @return self
     * @throws \InvalidArgumentException
     */
    public static function create() : self
    {

        static $cache = [];
        //$ip = !isset($_SERVER['SERVER_ADDR']) || is_array($_SERVER['SERVER_ADDR']) ? gethostname() : $_SERVER['SERVER_ADDR'];
        $ip = gethostname();

        if (isset($cache[$ip]) === FALSE) {
            $ipList = self::$ip;
            foreach ($ipList as $env => $_ip) {
                if (in_array($ip, $_ip, TRUE)) {
                    $cache[$ip] = new self($env);
                    break;
                }
            }
        }
        $environment = $cache[$ip] ?? new Environment(self::PRODUCTION);
        if ($environment === NULL) {
            throw new \InvalidArgumentException(sprintf('Unknown ip address \'%s\'.', $ip));
        }
        return $environment;
    }
}
