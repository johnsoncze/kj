<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Filtration\Filter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ColorList extends FilterParameters
{


    /**
     * @return string
     */
    public function getType() : string
    {
        return 'color_list';
    }
}