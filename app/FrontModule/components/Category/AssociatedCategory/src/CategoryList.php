<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\AssociatedCategory;

use App\Category\CategoryEntity;
use App\Category\CategoryFindFacadeFactory;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CategoryList extends Control
{


	/** @var CategoryEntity */
	private $category;

	/** @var CategoryFindFacadeFactory */
	private $categoryFindFacadeFactory;



	public function __construct(CategoryFindFacadeFactory $categoryFindFacadeFactory)
	{
		parent::__construct();
		$this->categoryFindFacadeFactory = $categoryFindFacadeFactory;
	}



	/**
	 * @param $category CategoryEntity
	 */
	public function setCategory(CategoryEntity $category)
	{
		$this->category = $category;
	}



	public function render()
	{
		$categoryFindFacade = $this->categoryFindFacadeFactory->create();
		$associatedCategories = $categoryFindFacade->findAssociatedCategoriesById($this->category->getId());

		$this->template->categories = $associatedCategories;
		$this->template->setFile(__DIR__ . '/default.latte');
		$this->template->render();
	}
}