<?php

namespace App\Components\SeoFormContainer\IndexFollowForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IndexFollowFormFactory
{


    /**
     * @return IndexFollowForm
     */
    public function create();
}