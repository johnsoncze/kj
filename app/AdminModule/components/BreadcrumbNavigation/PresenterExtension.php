<?php

namespace App\Components\BreadcrumbNavigation;

use App\Helpers\Presenters;
use Nette\Application\IPresenter;
use Nette\Application\UI\Presenter;
use App\NObject;
use Nette\Reflection\ClassType;


class PresenterExtension extends NObject implements IExtension
{


    /** @var string */
    const DEFAULT_ACTION = "default";

    /** @var string */
    const PARENT_ANNOTATION = "breadcrumb-nav-parent";

    /** @var INameResolver */
    protected $nameResolver;

    /** @var Navigation */
    protected $navigation;

    /** @var Presenter */
    protected $presenter;



    public function __construct(Presenter $presenter, INameResolver $nameResolver)
    {
        $this->presenter = $presenter;
        $this->nameResolver = $nameResolver;
    }



    /**
     * Load extension
     * @param $navigation Navigation
     * @return void
     */
    public function load(Navigation $navigation)
    {
        $this->navigation = $navigation;
        $name = $this->presenter->getName();
        $action = $this->presenter->getAction();
        $this->loadParents($this->presenter);
        if ($action != self::DEFAULT_ACTION) {
            $this->addLink(Presenters::getRoute($name, self::DEFAULT_ACTION));
        }
        $this->addLink(Presenters::getRoute($name, $action));
    }



    /**
     * Load parents
     * @param $presenter IPresenter
     * @return void
     */
    protected function loadParents(IPresenter $presenter)
    {
        $classType = new ClassType($presenter);
        $annotations = $classType->getAnnotations();
        foreach (isset($annotations[self::PARENT_ANNOTATION]) ? $annotations[self::PARENT_ANNOTATION] : [] as $route) {
            $this->addLink($route);
        }
    }



    /**
     * Add link
     * @param $route string
     * @return Link
     */
    protected function addLink($route)
    {
        $link = $this->navigation->addLink($route, $this->nameResolver->getName($route));
        return $link;
    }

}