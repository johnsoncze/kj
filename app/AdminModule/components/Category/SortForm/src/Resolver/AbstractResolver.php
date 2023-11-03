<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\SortForm\Resolver;

use App\Category\CategoryRepository;
use App\Category\CategorySaveFacadeFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class AbstractResolver
{


    /** @var CategorySaveFacadeFactory */
    protected $categoryFacadeFactory;

    /** @var CategoryRepository */
    protected $categoryRepo;



    public function __construct(CategoryRepository $categoryRepository,
                                CategorySaveFacadeFactory $categorySaveFacadeFactory)
    {
        $this->categoryFacadeFactory = $categorySaveFacadeFactory;
        $this->categoryRepo = $categoryRepository;
    }
}