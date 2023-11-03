<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\SortForm\Resolver;

use App\Language\LanguageEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ChildResolver extends AbstractResolver implements IResolver
{


    /**
     * @inheritdoc
     */
    public function match($key) : bool
    {
        return is_numeric($key);
    }



    /**
     * @inheritdoc
     * @throws \InvalidArgumentException
     */
    public function findItems(LanguageEntity $language, $arg = NULL) : array
    {
        if ($arg === NULL) {
            throw new \InvalidArgumentException('Missing argument.');
        }
        return $this->categoryRepo->findByParentCategoryId((int)$arg);
    }



    /**
     * @param $sorting array
     */
    public function save(array $sorting)
    {
        $categoryFacade = $this->categoryFacadeFactory->create();
        $categoryFacade->saveSort($sorting);
    }

}