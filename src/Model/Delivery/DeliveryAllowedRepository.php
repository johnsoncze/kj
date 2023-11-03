<?php

declare(strict_types = 1);

namespace App\Delivery;

use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\Repositories\Traits\ReadOnlyTrait;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class DeliveryAllowedRepository extends BaseRepository
{


    use ReadOnlyTrait;

    /** @var string */
    protected $entityName = Delivery::class;



    /**
     * @param int $id
     * @return Delivery
     * @throws DeliveryNotFoundException
     * @todo přeložit chybovou hlášku
     */
    public function getOneById(int $id) : Delivery
    {
        $delivery = $this->findOneBy([
            'where' => [
                ['id', '=', $id],
                $this->getCondition()
            ]
        ]);
        if (!$delivery) {
            throw new DeliveryNotFoundException(sprintf('Doprava s id "%d" nebyla nalezena.', $id));
        }
        return $delivery;
    }



    /**
     * @return Delivery[]|array
     */
    public function findAll() : array
    {
        $filter['sort'] = [['sort', 'LENGTH(sort)'], 'ASC'];
        $filter['where'][] = $this->getCondition();
        return $this->findBy($filter) ?: [];
    }



    /**
     * @return array
     */
    private function getCondition() : array
    {
        return ['state', '=', Delivery::ALLOWED];
    }
}