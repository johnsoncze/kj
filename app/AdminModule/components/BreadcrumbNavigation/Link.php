<?php

namespace App\Components\BreadcrumbNavigation;

use App\NObject;


/**
 * @method getLink()
 * @method getAnchor()
 * @method setClickable($bool)
 */
class Link extends NObject
{


    /** @var $link string */
    protected $link;

    /** @var $anchor string */
    protected $anchor;

    /** @var bool */
    protected $clickable = true;



    public function __construct($link, $anchor)
    {
        $this->link = $link;
        $this->anchor = $anchor;
    }



    /**
     * Is link clickable?
     * @return bool
     */
    public function isClickable()
    {
        return $this->clickable;
    }

}