<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\SortForm\Resolver;

use App\Category\CategoryEntity;
use App\Language\LanguageEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IResolver
{


    /**
     * @param $key mixed
     * @return bool
     */
    public function match($key) : bool;



    /**
     * @param $language LanguageEntity
     * @param $arg mixed
     * @return array|CategoryEntity[]
     */
    public function findItems(LanguageEntity $language, $arg = NULL) : array;



    /**
     * @param $sorting array
     * @return void
     */
    public function save(array $sorting);
}