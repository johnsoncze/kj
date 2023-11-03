<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroup;

use App\Category\CategoryEntity;
use App\Category\CategoryNotFoundException;
use App\Category\CategoryRepositoryFactory;
use App\Libs\FileManager\FileManager;
use App\NotFoundException;
use Nette\Http\FileUpload;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationGroupSaveFacade extends NObject
{


	/** @var CategoryFiltrationGroupRepositoryFactory */
	protected $categoryFiltrationGroupRepositoryFactory;

	/** @var CategoryRepositoryFactory */
	protected $categoryRepositoryFactory;

	/** @var FileManager */
	protected $fileManager;



	public function __construct(CategoryRepositoryFactory $categoryRepositoryFactory,
								CategoryFiltrationGroupRepositoryFactory $categoryFiltrationGroupRepositoryFactory,
								FileManager $fileManager)
	{
		$this->categoryRepositoryFactory = $categoryRepositoryFactory;
		$this->categoryFiltrationGroupRepositoryFactory = $categoryFiltrationGroupRepositoryFactory;
		$this->fileManager = $fileManager;
	}



	/**
	 * @param CategoryEntity $categoryEntity
	 * @param string|NULL $description
	 * @param string|NULL $titleSeo
	 * @param string|NULL $descriptionSeo
	 * @param bool $index
	 * @param bool $follow
	 * @param string $status
	 * @param $showInMenu bool
	 * @return CategoryFiltrationGroupEntity
	 * @throws CategoryFiltrationGroupSaveFacadeException
	 */
	public function add(CategoryEntity $categoryEntity,
						string $description = NULL,
						string $titleSeo = NULL,
						string $descriptionSeo = NULL,
						bool $index = TRUE,
						bool $follow = TRUE,
						string $status = CategoryFiltrationGroupEntity::PUBLISH,
						bool $showInMenu = FALSE) : CategoryFiltrationGroupEntity
	{
		//create entity
		$factory = new CategoryFiltrationGroupEntityFactory();
		$entity = $factory->create($categoryEntity->getId(), $description, $titleSeo, $descriptionSeo, $index, NULL, $follow, $status);
		$entity->setShowInMenu($showInMenu);

		try {
			$this->loadCategory($categoryEntity->getId());
			$this->setSiteMap($entity);

			//save
			$repo = $this->categoryFiltrationGroupRepositoryFactory->create();
			$repo->save($entity);

			return $entity;
		} catch (CategoryNotFoundException $exception) {
			throw new CategoryFiltrationGroupSaveFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param CategoryFiltrationGroupEntity $entity
	 * @return CategoryFiltrationGroupEntity
	 * @throws CategoryFiltrationGroupSaveFacadeException
	 */
	public function update(CategoryFiltrationGroupEntity $entity) : CategoryFiltrationGroupEntity
	{
		if (!$entity->getId()) {
			throw new CategoryFiltrationGroupSaveFacadeException(sprintf("For save a new group use 'add(...)' method."));
		}

		try {
			$this->loadCategory($entity->getCategoryId());
			$this->setSiteMap($entity);

			//save
			$repo = $this->categoryFiltrationGroupRepositoryFactory->create();
			$repo->save($entity);

			return $entity;
		} catch (CategoryNotFoundException $exception) {
			throw new CategoryFiltrationGroupSaveFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param $sorting array [id => sorting,..]
	 * @return bool
	 */
	public function saveSort(array $sorting) : bool
	{
		$groupId = array_keys($sorting);
		$groupRepo = $this->categoryFiltrationGroupRepositoryFactory->create();
		$groups = $groupRepo->findById($groupId);
		foreach ($sorting as $_groupId => $_sort) {
			$group = $groups[$_groupId];
			$group->setSort($_sort);
			$groupRepo->save($group);
		}
		return TRUE;
	}



	/**
	 * @param $id int
	 * @param $thumbnail FileUpload
	 * @return bool
	 * @throws CategoryFiltrationGroupSaveFacadeException
	 */
	public function saveImages(int $id, FileUpload $thumbnail) : bool
	{
		try {
			if ($thumbnail->hasFile()) {
				if ($thumbnail->isImage() !== TRUE) {
					throw new CategoryFiltrationGroupSaveFacadeException('Miniatura musí být obrázek.');
				}
				$repo = $this->categoryFiltrationGroupRepositoryFactory->create();
				$group = $repo->getOneById($id);
				$category = $this->loadCategory((int)$group->getCategoryId());
				$this->fileManager->setFolder($group->getUploadFolder($category));
				$fileName = $this->fileManager->upload($thumbnail, NULL, TRUE);
				$this->fileManager->flush();

				$group->setThumbnailImage($fileName);
				$repo->save($group);
			}
			return TRUE;
		} catch (CategoryFiltrationGroupNotFoundException $exception) {
			throw new CategoryFiltrationGroupSaveFacadeException($exception->getMessage());
		} catch (CategoryNotFoundException $exception) {
			throw new CategoryFiltrationGroupSaveFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param $id int group id
	 * @return CategoryFiltrationGroupEntity
	 * @throws CategoryFiltrationGroupSaveFacadeException
	 */
	public function deleteThumbnail(int $id) : CategoryFiltrationGroupEntity
	{
		try {
			$repo = $this->categoryFiltrationGroupRepositoryFactory->create();
			$group = $repo->getOneById($id);
			$category = $this->loadCategory($group->getCategoryId());
			if ($group->getThumbnailImage()) {
				@unlink($this->fileManager->getDirs()['dir'] . DIRECTORY_SEPARATOR . $group->getUploadFolder($category) . DIRECTORY_SEPARATOR . $group->getThumbnailImage());
				$group->setThumbnailImage(NULL);
				$repo->save($group);
			}
			return $group;
		} catch (CategoryFiltrationGroupNotFoundException $exception) {
			throw new CategoryFiltrationGroupSaveFacadeException($exception->getMessage());
		} catch (CategoryNotFoundException $exception) {
			throw new CategoryFiltrationGroupSaveFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param CategoryFiltrationGroupEntity $entity
	 * @return CategoryFiltrationGroupEntity
	 */
	protected function setSiteMap(CategoryFiltrationGroupEntity $entity) : CategoryFiltrationGroupEntity
	{
		$setter = new CategoryFiltrationGroupSetSiteMap();
		$setter->set($entity);
		return $entity;
	}



	/**
	 * @param int $categoryId
	 * @return CategoryEntity
	 */
	protected function loadCategory(int $categoryId) : CategoryEntity
	{
		$categoryRepo = $this->categoryRepositoryFactory->create();
		return $categoryRepo->getOneById($categoryId);
	}
}