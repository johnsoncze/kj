<?php

declare(strict_types = 1);

namespace App\Category;

use App\Category\AssociatedCategory\CategoryRepository AS AssociatedCategoryRepository;
use App\CategoryProductParameter\CategoryProductParameterEntity;
use App\CategoryProductParameter\CategoryProductParameterRepository;
use App\Helpers\Entities;
use App\Product\Parameter\ProductParameterRepository;
use Kdyby\Monolog\Logger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CategoryFindFacade
{

	/** @var AssociatedCategoryRepository */
	private $associatedCategoryRepo;

    /** @var CategoryProductParameterRepository */
    private $categoryProductParameterRepo;

    /** @var CategoryRepository */
    private $categoryRepo;

    /** @var Logger */
    private $logger;

    /** @var ProductParameterRepository */
    private $productParameterRepo;



    public function __construct(AssociatedCategoryRepository $associatedCategoryRepo,
								CategoryProductParameterRepository $categoryProductParameterRepo,
                                CategoryRepository $categoryRepository,
                                Logger $logger,
                                ProductParameterRepository $productParameterRepository)
    {
    	$this->associatedCategoryRepo = $associatedCategoryRepo;
        $this->categoryProductParameterRepo = $categoryProductParameterRepo;
        $this->categoryRepo = $categoryRepository;
        $this->logger = $logger;
        $this->productParameterRepo = $productParameterRepository;
    }



    /**
	 * @param $id int
	 * @return CategoryEntity[]|array
    */
	public function findAssociatedCategoriesById(int $id) : array
	{
		$categories = [];
		$associatedCategories = $this->associatedCategoryRepo->findByCategoryId($id);
		if ($associatedCategories) {
			$associatedCategoriesId = Entities::getProperty($associatedCategories, 'associatedCategoryId');
			$catalogCategories = $this->categoryRepo->findPublishedByMoreId($associatedCategoriesId);
			foreach ($associatedCategories as $associatedCategory) {
				$associatedCategory = $catalogCategories[$associatedCategory->getAssociatedCategoryId()] ?? NULL;
				$associatedCategory && $categories[] = $associatedCategory;
			}
		}
		return $categories;
	}



    /**
     * @param $productId int
     * @return CategoryEntity[]|array
     * todo test
     */
    public function findByProductId(int $productId) : array
    {
        $categoryParameters = $this->getCategoryParametersByProductId($productId);
        return $categoryParameters ? $this->getSortedCategoriesByMoreId(array_keys($categoryParameters)) : [];
    }



    /**
     * @param $productId int
     * @return CategoryEntity[]|array
     * todo test
     */
    public function findPublishedByProductId(int $productId) : array
    {
        $categoryParameters = $this->getCategoryParametersByProductId($productId);
        !$categoryParameters ? $this->logger->addNotice(sprintf('Produkt s id \'%d\' nemá žádnou publikovanou kategorii.', $productId)) : NULL;
        return $categoryParameters ? $this->getSortedCategoriesByMoreId(array_keys($categoryParameters)) : [];
    }



    /**
     * @param $productId int
     * @return CategoryProductParameterEntity[]|array
     */
    private function getCategoryParametersByProductId(int $productId) : array
    {
        $productParameters = $this->productParameterRepo->findByProductId($productId);
        if ($productParameters) {
            $parameterId = Entities::getProperty($productParameters, 'parameterId');
            $categoryParameters = $this->categoryProductParameterRepo->findWhichContainAtLeastOneOfMoreParameterId($parameterId);
            $categoryParameters = Entities::toSegment($categoryParameters, 'categoryId');
            foreach ($categoryParameters as $key => $parameter) {
                foreach ($parameter as $p) {
                    //if a category parameter is not one of product parameters, remove category
                    if (!in_array($p->getProductParameterId(), $parameterId)) {
                        unset($categoryParameters[$key]);
                        break;
                    }
                }
            }
        }
        return $categoryParameters ?? [];
    }



    /**
     * @param $categoryId int[]
     * @return CategoryEntity[]|array
     */
    private function getSortedCategoriesByMoreId(array $categoryId) : array
    {
        $_sortedCategories = [];
        $categories = $this->categoryRepo->findByMoreId($categoryId);
        foreach ($categories as $category) {
            $_sortedCategories[$category->getTextNavigation()] = $category;
        }
        ksort($_sortedCategories);
        return $_sortedCategories;
    }
}