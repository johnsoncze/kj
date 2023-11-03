<?php

namespace App\Extensions\Latte;

use Latte\MacroNode;
use Latte\PhpWriter;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Macros extends NObject
{


    /**
     * @param MacroNode $node
     * @param PhpWriter $phpWriter
     * @return string
     */
    public static function fill(MacroNode $node, PhpWriter $phpWriter)
    {
        return ' if(' . $node->args . '){ echo ' . $node->args . ';} else { echo "-"; }';
    }



    /**
     * @param MacroNode $node
     * @param PhpWriter $phpWriter
     * @return string
     */
    public static function boolicon(MacroNode $node, PhpWriter $phpWriter)
    {
        $sizeModifier = NULL;
        preg_match('/size:(\d)/', $node->modifiers, $sizeModifier);
        $size = $sizeModifier[1] ?? 1;
        return ' if('.$node->args.' === TRUE){ echo \'<i class="fa fa-check-circle fa-' . $size . 'x green va-middle"></i>\';} else { echo \'<i class="fa fa-times-circle fa-' . $size . 'x red va-middle"></i>\'; }';
    }
}