<?php

declare(strict_types = 1);

namespace App\Page;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageEntityFactory extends NObject
{


    /**
     * @param int $languageId
     * @param int|NULL $parentPageId
     * @param string $type
     * @param string $name
     * @param string|NULL $content
     * @param string|NULL $url
     * @param string|NULL $titleSeo
     * @param string|NULL $descriptionSeo
     * @param array|NULL $setting
     * @param string $status
     * @return PageEntity
     */
    public function create(int $languageId, int $parentPageId = NULL, string $type, string $name, string $content = NULL, string $url = NULL,
                           string $titleSeo = NULL, string $descriptionSeo = NULL, array $setting = NULL, string $status)
    {
        $page = new PageEntity();
        $page->setLanguageId($languageId);
        $page->setParentPageId($parentPageId);
        $page->setType($type);
        $page->setName($name);
        $page->setContent($content);
        $page->setUrl($url);
        $page->setTitleSeo($titleSeo);
        $page->setDescriptionSeo($descriptionSeo);
        $page->setSetting($setting);
        $page->setStatus($status);

        return $page;
    }
}