<?php

declare(strict_types = 1);

namespace App\Opportunity\Product;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = Product::class;



    /**
     * @param $id int
     * @return array|Product[]
     */
    public function findByOpportunityId(int $id) : array
    {
        $filters['where'][] = ['opportunityId', '=', $id];
        return $this->findBy($filters) ?: [];
    }
}