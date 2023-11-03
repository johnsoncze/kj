<?php

declare(strict_types = 1);

namespace App\Category;

use App\Category\Product\Sorting\SortingRepository;
use App\Libs\FileManager\FileManager;
use App\Url\UrlResolver;
use Nette\Http\FileUpload;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategorySaveFacade extends NObject
{


	/** @var SortingRepository */
	protected $categoryProductSortingRepo;

	/** @var CategoryRepositoryFactory */
	protected $categoryRepositoryFactory;

	/** @var FileManager */
	protected $fileManager;

	/** @var UrlResolver */
	protected $urlResolver;



	public function __construct(CategoryRepositoryFactory $categoryRepositoryFactory,
								FileManager $fileManager,
								SortingRepository $sortingRepo,
								UrlResolver $urlResolver)
	{
		$this->categoryRepositoryFactory = $categoryRepositoryFactory;
		$this->fileManager = $fileManager;
		$this->categoryProductSortingRepo = $sortingRepo;
		$this->urlResolver = $urlResolver;
	}



	/**
	 * @param int $languageId
	 * @param $parentId int|null
	 * @param string $name
	 * @param $content string|null
	 * @param string|NULL $url
	 * @param string|NULL $titleSeo
	 * @param string|NULL $descriptionSeo
	 * @param int|NULL $sort
	 * @param string $status
	 * @param $template string|null
	 * @param $showOnHomepage bool
	 * @param $categorySlider bool
	 * @param $imageTemplate string|null
	 * @param $top bool
	 * @return CategoryEntity
	 * @throws CategorySaveFacadeException
	 */
	public function add(int $languageId, int $parentId = NULL, string $name, string $content = NULL, string $url = NULL,
						string $titleSeo = NULL, string $descriptionSeo = NULL, int $sort = NULL, string $status, string $template = NULL,
						bool $showOnHomepage = FALSE, bool $categorySlider = FALSE, string $imageTemplate = NULL,
						bool $top = FALSE)
	: CategoryEntity
	{

		try {
			$repo = $this->categoryRepositoryFactory->create();

			$categoryFactory = new CategoryEntityFactory();
			$category = $categoryFactory->create($languageId, $parentId, $name, $content, $url, $titleSeo, $descriptionSeo, $sort, $status);
			$category->setTemplate($template);
			$category->setShowOnHomepage($showOnHomepage);
			$category->setCategorySlider($categorySlider);
			$category->setImageTemplate($imageTemplate);
			$category->setUrl($this->urlResolver->getAvailableUrl($url ?: $name, $repo, $languageId));
			$category->setTop($top);

			$this->process($category, $repo);

			return $category;
		} catch (CategoryCheckDuplicateException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		} catch (CategoryCheckParentDepthException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param CategoryEntity $categoryEntity
	 * @return CategoryEntity
	 * @throws CategorySaveFacadeException
	 */
	public function update(CategoryEntity $categoryEntity) : CategoryEntity
	{
		try {
			$repo = $this->categoryRepositoryFactory->create();
			$category = $repo->getOneById($categoryEntity->getId());
			if ($category->getUrl() !== $categoryEntity->getUrl()) {
				$categoryEntity->setUrl($this->urlResolver->getAvailableUrl($categoryEntity->getUrl() ?: $categoryEntity->getName(), $repo, (int)$categoryEntity->getLanguageId()));
			}
			$this->process($categoryEntity, $repo);

			return $categoryEntity;
		} catch (CategoryCheckDuplicateException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		} catch (CategoryCheckParentDepthException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		} catch (CategoryNotFoundException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param $sorting [categoryId => sorting,..]
	 * @return bool
	 */
	public function saveSort(array $sorting) : bool
	{
		$categoryId = array_keys($sorting);
		$categoryRepo = $this->categoryRepositoryFactory->create();
		$categories = $categoryRepo->findByMoreId($categoryId);
		foreach ($sorting as $_categoryId => $_sorting) {
			$category = $categories[$_categoryId];
			$category->setSort($_sorting);
			$categoryRepo->save($category);
		}
		return TRUE;
	}



	/**
	 * @param $sorting [categoryId => sorting,..]
	 * @return bool
	 */
	public function saveHomepageSort(array $sorting) : bool
	{
		$categoryId = array_keys($sorting);
		$categoryRepo = $this->categoryRepositoryFactory->create();
		$categories = $categoryRepo->findByMoreId($categoryId);
		foreach ($sorting as $_categoryId => $_sorting) {
			$category = $categories[$_categoryId];
			$category->setHomepageSort($_sorting);
			$categoryRepo->save($category);
		}
		return TRUE;
	}



	/**
	 * @param $sorting [categoryId => sorting,..]
	 * @return bool
	 */
	public function saveCategorySliderSort(array $sorting) : bool
	{
		$categoryId = array_keys($sorting);
		$categoryRepo = $this->categoryRepositoryFactory->create();
		$categories = $categoryRepo->findByMoreId($categoryId);
		foreach ($sorting as $_categoryId => $_sorting) {
			$category = $categories[$_categoryId];
			$category->setCategorySliderSort($_sorting);
			$categoryRepo->save($category);
		}
		return TRUE;
	}



	/**
	 * todo can be merged to ::add(), ::update()
	 * @param $categoryId int
	 * @param $showInCategorySlider bool
	 * @param $productSorter int|null
	 * @return CategoryEntity
	 * @throws CategorySaveFacadeException
	 */
	public function setAdvancedSettings(int $categoryId, bool $showInCategorySlider, int $productSorter = NULL) : CategoryEntity
	{
		try {
			$categoryRepo = $this->categoryRepositoryFactory->create();
			$category = $categoryRepo->getOneById($categoryId);
			$category->setCategorySlider($showInCategorySlider);
			$category->setProductSorter($productSorter);
			$categoryRepo->save($category);

			if ($productSorter === NULL) {
				$this->categoryProductSortingRepo->deleteByCategoryId($category->getId());
			}

			return $category;
		} catch (CategoryNotFoundException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param $categoryId int
	 * @param $general FileUpload
	 * @param $thumbnail FileUpload
	 * @param $generalDesktop FileUpload
	 * @param $generalMobile FileUpload
	 * @param $collection FileUpload
	 * @param $subcategory FileUpload
	 * @return CategoryEntity
	 * @throws CategorySaveFacadeException
	 * todo optimized
	 */
	public function saveImages(int $categoryId, FileUpload $general = NULL, FileUpload $thumbnail = NULL,
														FileUpload $generalDesktop = NULL, FileUpload $generalMobile = NULL,
														FileUpload $collection = NULL, FileUpload $subcategory = NULL) : CategoryEntity
	{
		try {
			$categoryRepo = $this->categoryRepositoryFactory->create();
			$category = $categoryRepo->getOneById($categoryId);

			//general photo
			if ($general->hasFile()) {
				if ($general->isImage() !== TRUE) {
					throw new CategorySaveFacadeException('Úvodní fotografie musí být obrázek.');
				}
				$this->fileManager->setFolder($category->getUploadFolder());
				$fullName = $this->fileManager->upload($general, NULL, TRUE);
				$this->fileManager->flush();

				$category->setGeneralImage($fullName);
			}

			//thumbnail photo
			if ($thumbnail->hasFile()) {
				if ($thumbnail->isImage() !== TRUE) {
					throw new CategorySaveFacadeException('Miniatura musí být obrázek.');
				}

				$this->fileManager->setFolder($category->getUploadFolder());
				$fullName = $this->fileManager->upload($thumbnail, NULL, TRUE);
				$this->fileManager->flush();

				$category->setMenuImage($fullName);
			}

			//general desktop photo
			if ($generalDesktop->hasFile()) {
				if ($generalDesktop->isImage() !== TRUE) {
					throw new CategorySaveFacadeException('Úvodní fotografie desktop musí být obrázek.');
				}
				$this->fileManager->setFolder($category->getUploadFolder());
				$fullName = $this->fileManager->upload($generalDesktop, NULL, TRUE);
				$this->fileManager->flush();

				$category->setGeneralImageDesktop($fullName);
			}
			
			//general mobile photo
			if ($generalMobile->hasFile()) {
				if ($generalMobile->isImage() !== TRUE) {
					throw new CategorySaveFacadeException('Úvodní fotografie mobil musí být obrázek.');
				}
				$this->fileManager->setFolder($category->getUploadFolder());
				$fullName = $this->fileManager->upload($generalMobile, NULL, TRUE);
				$this->fileManager->flush();

				$category->setGeneralImageMobile($fullName);
			}			
			
			//collection image
			if ($collection->hasFile()) {
				if ($collection->isImage() !== TRUE) {
					throw new CategorySaveFacadeException('Úvodní fotografie mobil musí být obrázek.');
				}
				$this->fileManager->setFolder($category->getUploadFolder());
				$fullName = $this->fileManager->upload($collection, NULL, TRUE);
				$this->fileManager->flush();

				$category->setCollectionImage($fullName);
			}					
			
			//subcategory image
			if ($subcategory->hasFile()) {
				if ($subcategory->isImage() !== TRUE) {
					throw new CategorySaveFacadeException('Úvodní fotografie mobil musí být obrázek.');
				}
				$this->fileManager->setFolder($category->getUploadFolder());
				$fullName = $this->fileManager->upload($subcategory, NULL, TRUE);
				$this->fileManager->flush();

				$category->setSubcategoryImage($fullName);
			}						
			
			$categoryRepo->save($category);

			return $category;
		} catch (CategoryNotFoundException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param $id int category id
	 * @return CategoryEntity
	 * @throws CategorySaveFacadeException
	*/
	public function deleteGeneralImage(int $id) : CategoryEntity
	{
		try{
			$categoryRepo = $this->categoryRepositoryFactory->create();
			$category = $categoryRepo->getOneById($id);
			if ($category->getGeneralImage()) {
				@unlink($this->fileManager->getDirs()['dir'] . DIRECTORY_SEPARATOR . $category->getUploadFolder() . DIRECTORY_SEPARATOR . $category->getGeneralImage());
				$category->setGeneralImage(NULL);
				$categoryRepo->save($category);
			}
			return $category;
		} catch (CategoryNotFoundException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		}
	}


	/**
	 * @param $id int category id
	 * @return CategoryEntity
	 * @throws CategorySaveFacadeException
	*/
	public function deleteGeneralImageDesktop(int $id) : CategoryEntity
	{
		try{
			$categoryRepo = $this->categoryRepositoryFactory->create();
			$category = $categoryRepo->getOneById($id);
			if ($category->getGeneralImageDesktop()) {
				@unlink($this->fileManager->getDirs()['dir'] . DIRECTORY_SEPARATOR . $category->getUploadFolder() . DIRECTORY_SEPARATOR . $category->getGeneralImageDesktop());
				$category->setGeneralImageDesktop(NULL);
				$categoryRepo->save($category);
			}
			return $category;
		} catch (CategoryNotFoundException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		}
	}
	
	
	/**
	 * @param $id int category id
	 * @return CategoryEntity
	 * @throws CategorySaveFacadeException
	*/
	public function deleteGeneralImageMobile(int $id) : CategoryEntity
	{
		try{
			$categoryRepo = $this->categoryRepositoryFactory->create();
			$category = $categoryRepo->getOneById($id);
			if ($category->getGeneralImageMobile()) {
				@unlink($this->fileManager->getDirs()['dir'] . DIRECTORY_SEPARATOR . $category->getUploadFolder() . DIRECTORY_SEPARATOR . $category->getGeneralImageMobile());
				$category->setGeneralImageMobile(NULL);
				$categoryRepo->save($category);
			}
			return $category;
		} catch (CategoryNotFoundException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		}
	}
		
	
	/**
	 * @param $id int category id
	 * @return CategoryEntity
	 * @throws CategorySaveFacadeException
	*/
	public function deleteCollectionImage(int $id) : CategoryEntity
	{
		try{
			$categoryRepo = $this->categoryRepositoryFactory->create();
			$category = $categoryRepo->getOneById($id);
			if ($category->getCollectionImage()) {
				@unlink($this->fileManager->getDirs()['dir'] . DIRECTORY_SEPARATOR . $category->getUploadFolder() . DIRECTORY_SEPARATOR . $category->getCollectionImage());
				$category->setCollectionImage(NULL);
				$categoryRepo->save($category);
			}
			return $category;
		} catch (CategoryNotFoundException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		}	
	}
	
	
	/**
	 * @param $id int category id
	 * @return CategoryEntity
	 * @throws CategorySaveFacadeException
	*/
	public function deleteSubcategoryImage(int $id) : CategoryEntity
	{
		try{
			$categoryRepo = $this->categoryRepositoryFactory->create();
			$category = $categoryRepo->getOneById($id);
			if ($category->getSubcategoryImage()) {
				@unlink($this->fileManager->getDirs()['dir'] . DIRECTORY_SEPARATOR . $category->getUploadFolder() . DIRECTORY_SEPARATOR . $category->getSubcategoryImage());
				$category->setSubcategoryImage(NULL);
				$categoryRepo->save($category);
			}
			return $category;
		} catch (CategoryNotFoundException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		}	
	}
	
	
	/**
	 * @param $id int category id
	 * @return CategoryEntity
	 * @throws CategorySaveFacadeException
	 */
	public function deleteThumbnailImage(int $id) : CategoryEntity
	{
		try{
			$categoryRepo = $this->categoryRepositoryFactory->create();
			$category = $categoryRepo->getOneById($id);
			if ($category->getMenuImage()) {
				@unlink($this->fileManager->getDirs()['dir'] . DIRECTORY_SEPARATOR . $category->getUploadFolder() . DIRECTORY_SEPARATOR . $category->getMenuImage());
				$category->setMenuImage(NULL);
				$categoryRepo->save($category);
			}
			return $category;
		} catch (CategoryNotFoundException $exception) {
			throw new CategorySaveFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param CategoryEntity $categoryEntity
	 * @param CategoryRepository $categoryRepository
	 * @return CategoryEntity
	 */
	protected function process(CategoryEntity $categoryEntity, CategoryRepository $categoryRepository)
	: CategoryEntity
	{
		$this->checkDuplicate($categoryEntity, $categoryRepository);
		$categoryRepository->save($categoryEntity);
		$this->checkParentDepth($categoryEntity->getId(), $categoryRepository);

		return $categoryEntity;
	}



	/**
	 * @param CategoryEntity $categoryEntity
	 * @param CategoryRepository $repo
	 * @return CategoryEntity
	 */
	protected function checkDuplicate(CategoryEntity $categoryEntity, CategoryRepository $repo)
	: CategoryEntity
	{
		$nameDuplicateEntity = $repo->findOneByLanguageIdAndName($categoryEntity->getLanguageId(), $categoryEntity->getName());
		$urlDuplicateEntity = $repo->findOneByLanguageIdAndUrl($categoryEntity->getLanguageId(), $categoryEntity->getUrl());

		//Check
		$duplicateCheck = new CategoryCheckDuplicate();
		$duplicateCheck->checkName($categoryEntity, $nameDuplicateEntity);
		$duplicateCheck->checkUrl($categoryEntity, $urlDuplicateEntity);

		return $categoryEntity;
	}



	/**
	 * @param int $categoryId
	 * @param CategoryRepository $repository
	 * @return CategoryEntity
	 */
	protected function checkParentDepth(int $categoryId, CategoryRepository $repository)
	{
		//load from database for load all parent categories
		$category = $repository->getOneById($categoryId);
		$checker = new CategoryCheckParentDepth();
		$checker->check($category);
		return $category;
	}
}