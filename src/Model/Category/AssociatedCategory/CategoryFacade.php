<?php

declare(strict_types = 1);

namespace App\Category\AssociatedCategory;

use App\Category\AssociatedCategory\Category AS AssociatedCategory;
use App\Category\AssociatedCategory\CategoryRepository AS AssociatedCategoryRepo;
use App\Category\CategoryEntity;
use App\Category\CategoryNotFoundException;
use App\Category\CategoryRepository;
use App\NotFoundException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CategoryFacade
{


	/** @var AssociatedCategoryRepo */
	private $associatedCategoryRepository;

	/** @var CategoryRepository */
	private $categoryRepo;



	public function __construct(AssociatedCategoryRepo $associatedCategoryRepo,
								CategoryRepository $categoryRepo)
	{
		$this->associatedCategoryRepository = $associatedCategoryRepo;
		$this->categoryRepo = $categoryRepo;
	}



	/**
	 * @param $id int
	 * @return void
	 * @throws CategoryFacadeException
	 */
	public function delete(int $id)
	{
		try {
			$category = $this->associatedCategoryRepository->getOneById($id);
			$this->associatedCategoryRepository->remove($category);
		} catch (NotFoundException $exception) {
			throw new CategoryFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param $id int|null
	 * @param $categoryId int
	 * @param $associatedCategoryId int
	 * @return Category
	 * @throws CategoryFacadeException
	 */
	public function save(int $id = NULL,
						 int $categoryId,
						 int $associatedCategoryId) : Category
	{
		try {
			$associatedCategory = $id !== NULL ? $this->associatedCategoryRepository->getOneById($id) : new AssociatedCategory();
			$category = $this->categoryRepo->getOneById($categoryId);
			$associatingCategory = $this->categoryRepo->getOneById($associatedCategoryId);
			$this->checkValidity($category, $associatingCategory);

			$associatedCategory->setCategoryId($category->getId());
			$associatedCategory->setAssociatedCategoryId($associatingCategory->getId());
			$this->checkDuplicity($associatedCategory);
			$this->associatedCategoryRepository->save($associatedCategory);

			return $associatedCategory;
		} catch (NotFoundException $exception) {
			throw new CategoryFacadeException($exception->getMessage());
		} catch (CategoryNotFoundException $exception) {
			throw new CategoryFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param $category AssociatedCategory
	 * @return AssociatedCategory
	 * @throws CategoryFacadeException
	 */
	private function checkDuplicity(AssociatedCategory $category) : AssociatedCategory
	{
		$duplicateCategory = $this->associatedCategoryRepository->findOneByCategoryIdAndCategoryAssociatedId($category->getCategoryId(), $category->getAssociatedCategoryId());
		if ($duplicateCategory && $duplicateCategory->getId() !== $category->getId()) {
			throw new CategoryFacadeException('Přidružená kategorie již existuje.');
		}
		return $category;
	}



	/**
	 * @param $category CategoryEntity
	 * @param $associatedCategory CategoryEntity
	 * @return void
	 * @throws CategoryFacadeException
	 */
	private function checkValidity(CategoryEntity $category, CategoryEntity $associatedCategory)
	{
		if ($category->getId() === $associatedCategory->getId()) {
			throw new CategoryFacadeException('Nelze přiřadit stejnou kategorii.');
		}
	}
}