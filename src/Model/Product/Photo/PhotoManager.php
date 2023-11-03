<?php

declare(strict_types = 1);

namespace App\Product\Photo;

use App\Helpers\Images;
use App\Libs\FileManager\FileManager;
use App\Product\Product;
use Nette\Http\FileUpload;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PhotoManager
{


	/** @var FileManager */
	protected $fileManager;



	public function __construct(FileManager $fileManager)
	{
		$this->fileManager = $fileManager;
	}



	/**
	 * @param $source IPhoto
	 * @param $target Product
	 * @return string file name with extension
	 * @throws PhotoManagerException
	 */
	public function copy(IPhoto $source, Product $target) : string
	{
		if ($source->getPhotoName()) {
			$baseDir = realpath($this->fileManager->getDirs()['dir']);
			$photo = $baseDir . sprintf('/%s/%s', $source->getUploadFolder(), $source->getPhotoName());
			$temporaryName = sprintf('%s-%s', time(), $source->getPhotoName());
			$temporary = $baseDir . sprintf('/%s/%s', $source->getUploadFolder(), $temporaryName);
			if (@copy($photo, $temporary) !== TRUE) {
				$message = sprintf('Nepodařilo se vytvořit dočasný soubor z \'%s\' do \'%s\'.', $photo, $temporary);
				throw new PhotoManagerException($message);
			}
			$file = $this->createFile($temporaryName, $temporary);
			return $this->upload($target, $file);
		}
	}



	/**
	 * @param $product Product
	 * @param $file FileUpload
	 * @param $replace bool replace file is exists
	 * @return string file name with extension
	 */
	public function upload(Product $product, FileUpload $file, bool $replace = FALSE) : string
	{
		$this->fileManager->setFolder($product->getUploadFolder());
		$name = $this->fileManager->upload($file, $product->createPhotoName(), $replace);
		$this->fileManager->flush();

		return $name;
	}



	/**
	 * @param $photo IPhoto
	 * @return void
	 */
	public function delete(IPhoto $photo)
	{
		if ($photo->getPhotoName()) {
			$pattern = $photo->getUploadFolder() . DIRECTORY_SEPARATOR . $photo->getPhotoName();
			$this->fileManager->deleteByPattern($pattern);
			$this->deleteThumbnails($photo);
		}
	}



	/**
	 * @param $photo IPhoto
	 * @return void
	 */
	public function deleteThumbnails(IPhoto $photo)
	{
		if ($photo->getPhotoName()) {
			$pattern = $photo->getUploadFolder() . DIRECTORY_SEPARATOR;
			$pattern .= '*_*_' . $photo->getPhotoName();
			$this->fileManager->deleteByPattern($pattern);
		}
	}



	/**
	 * @param $file FileUpload
	 * @return FileUpload
	 * @throws \InvalidArgumentException
	 */
	public function checkImage(FileUpload $file) : FileUpload
	{
		if ($file->isImage() !== TRUE) {
			$message = sprintf('Fotografie musí být ve formátu %s.', implode(',', Images::getMimeTypes()));
			throw new \InvalidArgumentException($message);
		}
		return $file;
	}



	/**
	 * @param $name string
	 * @param $path string
	 * @return FileUpload
	 */
	private function createFile(string $name, string $path) : FileUpload
	{
		return new FileUpload([
			'name' => $name,
			'type' => 'image/png',
			'size' => 999,
			'tmp_name' => $path,
			'error' => 0
		]);
	}
}