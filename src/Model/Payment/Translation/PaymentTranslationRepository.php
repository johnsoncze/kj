<?php

declare(strict_types = 1);

namespace App\Payment\Translation;

use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PaymentTranslationRepository extends BaseRepository implements IRepository
{


    /** @var string */
    protected $entityName = PaymentTranslation::class;
}