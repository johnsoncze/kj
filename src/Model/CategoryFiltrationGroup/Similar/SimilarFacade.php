<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroup\Similar;

use App\Category\CategoryRepository;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterRepository;
use App\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SimilarFacade
{


    /** @var CategoryRepository */
    private $categoryRepo;

    /** @var CategoryFiltrationGroupParameterRepository */
    private $groupParameterRepo;

    /** @var CategoryFiltrationGroupRepository */
    private $groupRepo;



    public function __construct(CategoryFiltrationGroupParameterRepository $categoryFiltrationGroupParameterRepository,
                                CategoryFiltrationGroupRepository $categoryFiltrationGroupRepository,
                                CategoryRepository $categoryRepository)
    {
        $this->categoryRepo = $categoryRepository;
        $this->groupParameterRepo = $categoryFiltrationGroupParameterRepository;
        $this->groupRepo = $categoryFiltrationGroupRepository;
    }



    /**
     * @param $productId int
     * @param $languageId int
     * @param $limit int
     * @return CategoryFiltrationGroupEntity[]|array
     */
    public function findByProductIdAndLanguageId(int $productId,
                                                 int $languageId,
                                                 int $limit = 6) : array
    {
        $groupResponse = [];
        $groupId = $this->groupParameterRepo->findSimilarGroupsIdForPublishedCategoriesByProductIdAndLanguageId($productId, $languageId, $limit);
        $groups = $groupId ? $this->groupRepo->findIndexedByMoreId($groupId) : [];
        if ($groups) {
            $categories = $this->categoryRepo->findPublishedByMoreId(Entities::getProperty($groups, 'categoryId'));
            foreach ($groupId as $id) {
                if (($group = $groups[$id] ?? NULL) && ($category = $categories[$group->getCategoryId()] ?? NULL)) {
                    $group->setCategory($category);
                    $groupResponse[$id] = $group;
                }
            }
        }
        return $groupResponse;
    }
}