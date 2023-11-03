<?php

declare(strict_types = 1);

namespace App\Product\Ring\Size;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="ring_size")
 *
 * @method setSize($size)
 * @method getSize()
 */
class Size extends BaseEntity implements IEntity
{


    /**
     * @Column(name="rs_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="rs_size")
     */
    protected $size;
}