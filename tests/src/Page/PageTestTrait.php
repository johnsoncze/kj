<?php

declare(strict_types = 1);

namespace App\Tests\Page;

use App\Page\PageEntity;
use Nette\Utils\Strings;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait PageTestTrait
{


    /**
     * @return PageEntity
     */
    private function createTestPage() : PageEntity
    {
        $page = new PageEntity();
        $page->setLanguageId(1);
        $page->setType(PageEntity::TEXT_TYPE);
        $page->setName('Name');
        $page->setUrl(Strings::webalize($page->getUrl()));
        $page->setMenuLocation(PageEntity::MENU_LOCATION_HEADER);
        $page->setStatus(PageEntity::PUBLISH);

        return $page;
    }
}