<?php

declare(strict_types = 1);

namespace App\Product\Photo;

use App\Libs\FileManager\FileManager;
use App\Product\Product;
use Nette\DI\Container;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait PhotoTrait
{


    /**
     * @param Product $product
     * @param $fileManager FileManager
     * @param $container Container
     * @return string
     */
    public function getThumbnailToPhoto(Product $product, FileManager $fileManager, Container $container) : string
    {
        $thumbWidth = 50;
        $thumbHeight = 50;
        try {
            $photoFolder = $product->getUploadFolder();
        }catch (\InvalidArgumentException $e){
            //nada
        }
        if (isset($photoFolder) && $product->getPhoto() !== NULL && is_file($fileManager->getDir()->getFullDir() . DIRECTORY_SEPARATOR . $photoFolder . DIRECTORY_SEPARATOR . $product->getPhoto())) {
            $fileManager->setFolder($photoFolder);
            $photo = sprintf('<a href="%s" class="fancybox"><img src="%s"></a>',
                $fileManager->getThumbnail($product->getPhoto(), 800, 800),
                $fileManager->getThumbnail($product->getPhoto(), $thumbWidth, $thumbHeight));
        } else {
            $actualDirs = $fileManager->getDirs();
            $fileManager->setDirs($container->getParameters()['systemFiles']);
            $photo = sprintf('<img src="%s">', $fileManager->getThumbnail('no-image-admin.png', $thumbWidth, $thumbHeight));
            $fileManager->setDirs($actualDirs);
        }
        $fileManager->flush();

        return $photo;
    }
}