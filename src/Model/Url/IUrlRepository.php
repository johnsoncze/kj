<?php

declare(strict_types = 1);

namespace App\Url;

use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IUrlRepository
{


    /**
     * @param $url string
     * @param $languageId int|null
     * @return IEntity
     */
    public function findOneByUrlAndLanguageId(string $url, int $languageId);
}