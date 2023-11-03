<?php

namespace App\Components\BreadcrumbNavigation;

use App\Helpers\Presenters;
use Kdyby\Translation\Translator;
use App\NObject;


class NameResolver extends NObject implements INameResolver
{


    /** @var Translator */
    protected $translator;



    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }



    /**
     * Get name of route
     * @param $route string
     * @return string
     */
    public function getName($route)
    {
        return $this->translator->translate("presenter" . self::serialize($route));
    }



    /**
     * Serialize route
     * @param $route string
     * @return string
     */
    public static function serialize($route)
    {
        return Presenters::serializeRoute($route);
    }
}