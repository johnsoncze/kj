<?php

namespace App\Libs\FileManager;

use App\Libs\FileManager\Exceptions\UploaderException;
use App\Libs\FileManager\Names\Name;
use Nette\Http\FileUpload;
use App\NObject;
use Nette\Utils\Strings;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Uploader extends NObject
{


    /** @var FileManager */
    protected $fileManager;



    /**
     * Uploader constructor.
     * @param FileManager $fileManager
     */
    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }



    /**
     * @param FileUpload $fileUpload
     * @param null $newName
     * @param $replace bool
	 * @param $targetDir string|null
     * @return string
     * @throws UploaderException
     */
    public function upload(FileUpload $fileUpload, $newName = NULL, bool $replace = FALSE, string $targetDir = NULL)
    {
        $dir = $targetDir ? $targetDir : $this->fileManager->getDir()->getFullDir();
        $name = $this->getName($fileUpload, $newName);

        $nameObject = Name::create($name);
        $name = $replace === TRUE ? $nameObject->getFullName() : (new Checker($nameObject, $dir))->check()->getFullName();

        $destinationFilePath = $dir . "/" . $name;
	    if (!file_exists($dir)) {
		    mkdir($dir, 0777, true);
	    }

        copy(
            $fileUpload->getTemporaryFile(),
            $destinationFilePath,
        );
        if (!is_file($destinationFilePath)) {
            throw new UploaderException("Nepodařilo se nahrát soubor '{$name}' na server.");
        }
        return $name;
    }



    /**
     * @param FileUpload $fileUpload
     * @param null $newName
     * @return string
     */
    protected function getName(FileUpload $fileUpload, $newName = NULL)
    {
        if ($newName !== NULL) {
            return sprintf('%s.%s', Strings::webalize($newName),
                pathinfo($fileUpload->getName(), PATHINFO_EXTENSION));
        }
        return $fileUpload->getSanitizedName();
    }
}