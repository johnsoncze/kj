<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\SortForm\Resolver;

use App\Language\LanguageEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class FirstLevelResolver extends AbstractResolver implements IResolver
{


    /** @var string */
    const KEY = 'first_level';



    /**
     * @inheritdoc
     */
    public function match($key) : bool
    {
        return $key === self::KEY;
    }



    /**
     * @inheritdoc
     */
    public function findItems(LanguageEntity $language, $arg = NULL) : array
    {
        return $this->categoryRepo->findByLanguageIdWithoutParentCategoryId($language->getId());
    }



    /**
     * @inheritdoc
     */
    public function save(array $sorting)
    {
        $categoryFacade = $this->categoryFacadeFactory->create();
        $categoryFacade->saveSort($sorting);
    }

}