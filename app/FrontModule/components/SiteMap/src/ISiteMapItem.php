<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\SiteMap;

use Nette\Application\LinkGenerator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ISiteMapItem
{


    /**
     * Get absolute url address.
     * @param $linkGenerator LinkGenerator
     * @return string
     */
    public function getLocation(LinkGenerator $linkGenerator) : string;



    /**
     * Get change frequency.
     * @return string|null
     */
    public function getChangeFrequency();



    /**
     * Get priority.
     * @return float|null
     */
    public function getPriority();
}