<?php

namespace App\Libs\FileManager\Macros;

use Latte\MacroNode;
use Latte\PhpWriter;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Thumbnail extends NObject
{


    /**
	 * Call: {thumbnailPath <width>, <height>, <filename>, <folder>}
	 *
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public static function thumbnailLink(MacroNode $node, PhpWriter $writer)
    {
        $args = explode(',', $node->args);
        $originalName = trim($args[2]);
        $width = trim($args[0]);
        $height = trim($args[1]);
        $folder = isset($args[3]) ? $args[3] : "null";
        $group = $args[4] ?? 'NULL';
        $alt = $args[5] ?? 'NULL';

        return '
                if(' . $folder . '){ $fileManager->setFolder(' . $folder . '); };?>
                <a href="<?php echo $fileManager->getThumbnail(' . $originalName . ',800,800) ?>" class="thumbnail item-photo"
                <?php if(' . $group .') {echo " rel=" . ' . $group . ';} ?>>
                <img src="<?php echo $fileManager->getThumbnail(' . $originalName . ',' . $width . ',' . $height . '); ?>"<?php if(' . $alt .') {echo " alt=\'" . ' . $alt . ' .  "\'";} ?>>
                </a>
                <?php 
                $fileManager->flush();';
    }



    /**
	 * Call: {thumbnailPath <width>, <height>, <filename>, <folder>}
	 *
     * @param $node MacroNode
     * @param $writer PhpWriter
     * @return string
    */
    public static function thumbnailPath(MacroNode $node, PhpWriter $writer)
    {
        $args = explode(',', $node->args);
        $originalName = trim($args[2]);
        $width = trim($args[0]);
        $height = trim($args[1]);
        $folder = $args[3] ?? 'null';

        return '
                if(' . $folder . '){ $fileManager->setFolder(' . $folder . '); };
                    echo $fileManager->getThumbnail(' . $originalName . ',' . $width . ',' . $height . ');
                $fileManager->flush();';
    }



    /**
	 * @param $node MacroNode
	 * @param $writer PhpWriter
	 * @return string
    */
	public static function imagePlaceholder(MacroNode $node, PhpWriter $writer)
	{
		$args = explode(',', $node->args);
		$width = trim($args[0]);
		$height = trim($args[1]);

		return '$fileManager->setFolder(\'../assets/front/user_content/images\');
				echo $fileManager->getThumbnail(\'placeholder.jpg\',' . $width . ',' . $height . ');
				$fileManager->flush();';
	}
}