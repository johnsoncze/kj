<?php

declare(strict_types = 1);

namespace App\Url;

use Nette\Utils\Strings;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class UrlResolver
{


    /**
     * @param $url string
     * @param $repository IUrlRepository
     * @param $languageId int
     * @return string
     */
    public function getAvailableUrl(string $url,
                                    IUrlRepository $repository,
                                    int $languageId) : string
    {
        $i = 1;
        $url = Strings::webalize($url);
        $temporaryUrl = $url;
        for (; ;) {
            if ($repository->findOneByUrlAndLanguageId($temporaryUrl, $languageId)) {
                $temporaryUrl = $url . '-' . $i;
                $i++;
                continue;
            }
            $url = $temporaryUrl;
            break;
        }
        return $url;
    }
}