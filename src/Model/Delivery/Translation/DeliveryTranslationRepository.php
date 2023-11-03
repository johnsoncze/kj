<?php

declare(strict_types = 1);

namespace App\Delivery\Translation;

use App\Extensions\Grido\IRepositorySource;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class DeliveryTranslationRepository extends BaseRepository implements IRepositorySource
{


    /** @var string */
    protected $entityName = DeliveryTranslation::class;
}