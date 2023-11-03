<?php

declare(strict_types = 1);

namespace App\Product\Ring\Size;

use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SizeRepository extends BaseRepository
{


    /** @var string */
    protected $entityName = Size::class;



    /**
     * @return Size[]|array
     */
    public function findAll()
    {
        $filter['sort'] = ['size', 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $id int
     * @return Size
     * @throws NotFoundException
     */
    public function getOneById(int $id) : Size
    {
        $filter['where'][] = ['id', '=', $id];
        $size = $this->findOneBy($filter);
        if (!$size) {
            throw new NotFoundException('Size not found.');
        }
        return $size;
    }
}