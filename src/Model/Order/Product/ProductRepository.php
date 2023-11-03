<?php

declare(strict_types = 1);

namespace App\Order\Product;

use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductRepository extends BaseRepository
{


    /** @var string */
    protected $entityName = Product::class;


}