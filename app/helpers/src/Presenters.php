<?php

namespace App\Helpers;

use Nette\Application\UI\Presenter;
use App\NObject;
use Nette\Utils\Strings;


/**
 * Helper for work with presenters
 *
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Presenters extends NObject
{


    /**
     * Convert route from "Admin:Login:default" to "admin.product.show"
     * @param $route string
     * @return string
     */
    public static function serializeRoute($route)
    {
        return Strings::lower(str_replace(":", ".", $route));
    }



    /**
     * Get full route etc ":Admin:Login:default"
     * @param $presenter Presenter
     * @return string
     */
    public static function getRouteFromPresenter(Presenter $presenter)
    {
        return self::getRoute($presenter->getName(), $presenter->getAction());
    }



    /**
     * Get full route etc ":Admin:Login:default"
     * @param $name string
     * @param $action string
     * @return string
     */
    public static function getRoute($name, $action)
    {
        return ":" . $name . ":" . $action;
    }
}