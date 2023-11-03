<?php

namespace App\Extensions\Latte;

use App\Libs\FileManager\Macros\Thumbnail;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class MacroSet extends \Latte\Macros\MacroSet
{


    public function __construct(\Latte\Compiler $compiler)
    {
        parent::__construct($compiler);
    }



    public static function install(\Latte\Compiler $compiler, \Kdyby\Translation\Translator $translator = null)
    {
        $set = new static($compiler);
        $set->addMacro('boolicon', [Macros::class, 'boolicon']);
        $set->addMacro("fill", [Macros::class, 'fill']);
        $set->addMacro("thumbnailLink", [Thumbnail::class, 'thumbnailLink']);
        $set->addMacro('thumbnailPath', [Thumbnail::class, 'thumbnailPath']);
        $set->addMacro('imagePlaceholder', [Thumbnail::class, 'imagePlaceholder']);
    }


}