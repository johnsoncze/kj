<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\Block\BannerSlim;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface BannerSlimFactory
{


    /**
     * @param $item Item
     * @return BannerSlim
     */
    public function create(Item $item);
}