<?php

namespace App\Libs\FileManager;

use App\Libs\FileManager\Thumbnails\Thumbnail;
use Kdyby\Monolog\Logger;
use Nette\Http\FileUpload;
use App\NObject;
use Nette\Utils\UnknownImageFileException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class FileManager extends NObject
{


    /** @var array */
    protected $dirs;

    /** @var Logger */
    protected $logger;

    /** @var string|null */
    protected $folder;



    public function __construct(array $dirs, Logger $logger)
    {
        $this->dirs = $dirs;
        $this->logger = $logger;
    }



    /**
     * @param $dirs array
     * @return self
     */
    public function setDirs(array $dirs) : self
    {
        $this->dirs = $dirs;
        return $this;
    }



    /**
     * @return array
     */
    public function getDirs() : array
    {
        return $this->dirs;
    }



    /**
     * @param $folder string
     * @return $this
     */
    public function setFolder(string $folder)
    {
        $this->folder = $folder;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getFolder()
    {
        return $this->folder;
    }



    /**
     * @param FileUpload $fileUpload
     * @param null $newName
     * @param $replace bool
	 * @param $targetDir string|null
     * @return string
     */
    public function upload(FileUpload $fileUpload, $newName = NULL, bool $replace = FALSE, string $targetDir = NULL)
    {
        $uploader = new Uploader($this);
        return $uploader->upload($fileUpload, $newName, $replace, $targetDir);
    }



    /**
     * @param $originName
     * @param $width
     * @param $height
     * @return string
     */
    public function getThumbnail($originName, $width, $height)
    {
    	try {
			$thumbnail = new Thumbnail($this, $originName, $width, $height);
			return $thumbnail->get();
		} catch (UnknownImageFileException $exception) {
    		$this->logger->addError($exception->getMessage(), [
    			'originName' => $originName,
				'width' => $width,
				'height' => $height,
			]);

    		$this->setFolder('../assets/front/user_content/images');
    		$placeholder = $this->getThumbnail('placeholder.jpg', $width, $height);
    		$this->flush();
    		return $placeholder;
    	}
    }



    /**
     * @param $name string
     * @param $dir string|null
     * @return string
    */
    public function getFile(string $name, string $dir = NULL)
    {
        $subdir = $dir ? $dir . DIRECTORY_SEPARATOR : '';
        return $this->getDir()->getBaseDir() . DIRECTORY_SEPARATOR . $subdir . $name;
    }



    /**
     * @return Dir
     */
    public function getDir()
    {
        $dirs = $this->dirs;
        $dirs["folder"] = $this->folder;

        return Dir::create($dirs);
    }



    /**
     * @return void
     */
    public function flush()
    {
        $this->folder = NULL;
    }



    /**
	 * @param $pattern string
	 * @return int count of deleted files
    */
    public function deleteByPattern(string $pattern)
	{
		$deleted = 0;
		$baseDir = $this->getDirs()['dir'];
		$files = glob($baseDir . DIRECTORY_SEPARATOR . $pattern);
		foreach ($files as $file) {
			unlink($file);
			$deleted++;
		}
		return $deleted;
	}
}