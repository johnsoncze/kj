<?php

declare(strict_types = 1);

namespace App\Product\AdditionalPhoto;

use App\BadFileTypeException;
use App\Libs\FileManager\Exceptions\UploaderException;
use App\Product\Photo\PhotoManager;
use App\Product\Product;
use Nette\Http\FileUpload;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductAdditionalPhotoSaveFacade extends NObject
{


	/** @var PhotoFactory */
	protected $photoFactory;

	/** @var PhotoManager */
	protected $photoManager;

	/** @var ProductAdditionalPhotoRepositoryFactory */
	protected $photoRepoFactory;



	public function __construct(PhotoFactory $photoFactory,
								PhotoManager $photoManager,
								ProductAdditionalPhotoRepositoryFactory $photoRepoFactory)
	{
		$this->photoFactory = $photoFactory;
		$this->photoManager = $photoManager;
		$this->photoRepoFactory = $photoRepoFactory;
	}



	/**
	 * @param $product Product
	 * @param $files FileUpload[]
	 * @return ProductAdditionalPhoto[]
	 * @throws ProductAdditionalPhotoSaveFacadeException
	 */
	public function add(Product $product, array $files) : array
	{
		try {
			$photos = [];

			foreach ($files as $file) {
				if (!$file instanceof FileUpload) {
					throw new ProductAdditionalPhotoSaveFacadeException(sprintf('Object must be instance of "%s".', FileUpload::class));
				}
				$name = $this->photoManager->upload($product, $file);
				$photos[] = $this->photoFactory->create($product, $name);
			}

			$photoRepo = $this->photoRepoFactory->create();
			$photoRepo->save($photos);

			return $photos;
		} catch (UploaderException $exception) {
			throw new ProductAdditionalPhotoSaveFacadeException($exception->getMessage());
		} catch (BadFileTypeException $exception) {
			throw new ProductAdditionalPhotoSaveFacadeException($exception->getMessage());
		}
	}



	/**
	 * @param int $photoId
	 * @return bool
	 * @throws ProductAdditionalPhotoSaveFacadeException
	 */
	public function remove(int $photoId) : bool
	{
		try {
			$photoRepo = $this->photoRepoFactory->create();
			$photo = $photoRepo->getOneById($photoId);
			$this->photoManager->delete($photo);
			$photoRepo->remove($photo);
			return TRUE;
		} catch (ProductAdditionalPhotoNotFoundException $exception) {
			throw new ProductAdditionalPhotoSaveFacadeException($exception->getMessage());
		}
	}
}