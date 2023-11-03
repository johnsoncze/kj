<?php

namespace App\Components\BreadcrumbNavigation;

use Nette\Application\UI\Control;


class Navigation extends Control
{


    /** @var array */
    protected $links = [];

    /** @var string */
    protected $homeLink = "/";



    /**
     * Set home link
     * @param $link string
     * @return self
     */
    public function setHomeLink($link)
    {
        $this->homeLink = (string)$link;
        return $this;
    }



    /**
     * Add link
     * @param $link string
     * @param $anchor string
     * @param $position int
     * @return Link
     */
    public function addLink($link, $anchor, $position = null)
    {
        $link = new Link($link, $anchor);
        if ($position === null) {
            $this->links[] = $link;
        } else {
            array_splice($this->links, (int)$position, 0, [$link]);
        }
        return $link;
    }



    /**
     * Extension
     * @param $extension IExtension
     * @return void
     */
    public function extension(IExtension $extension)
    {
        $extension->load($this);
    }



    /**
     * Render
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . '/templates/administration.latte');
        $this->template->links = $this->links;
        $this->template->homeLink = $this->homeLink;
        $this->template->render();
    }
}