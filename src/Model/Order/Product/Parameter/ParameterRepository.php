<?php

declare(strict_types = 1);

namespace App\Order\Product\Parameter;

use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ParameterRepository extends BaseRepository
{


    /** @var string */
    protected $entityName = Parameter::class;
}