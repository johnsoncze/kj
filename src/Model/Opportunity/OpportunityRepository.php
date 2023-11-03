<?php

declare(strict_types = 1);

namespace App\Opportunity;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OpportunityRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = Opportunity::class;



    /**
     * Get one by id.
     * @param $id int
     * @return Opportunity
     * @throws OpportunityNotFoundException
     */
    public function getOneById(int $id) : Opportunity
    {
        $filters['where'][] = ['id', '=', $id];
        $opportunity = $this->findOneBy($filters);
        if (!$opportunity) {
            throw new OpportunityNotFoundException('Opportunity not found.');
        }
        return $opportunity;
    }



    /**
     * @param $types array
     * @param $state string
     * @return CountDTO
    */
    public function getCountByTypesAndState(array $types, string $state) : CountDTO
    {
        $filter['where'][] = ['type', '', $types];
        $filter['where'][] = ['state', '=', $state];
        return $this->count($filter);
    }
}