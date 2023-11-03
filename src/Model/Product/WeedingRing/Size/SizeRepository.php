<?php

declare(strict_types = 1);

namespace App\Product\WeedingRing\Size;

use App\IRepository;
use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SizeRepository extends BaseRepository implements IRepository
{


    /** @var string */
    protected $entityName = Size::class;



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



    /**
     * @param $productId int
     * @param $sizeId int
     * @param $gender string
     * @return Size
     * @throws NotFoundException
    */
    public function getOneByProductIdAndSizeIdAndGender(int $productId, int $sizeId, string $gender) : Size
    {
        $filter['where'][] = ['productId', '=', $productId];
        $filter['where'][] = ['sizeId', '=', $sizeId];
        $filter['where'][] = ['gender', '=', $gender];
        $size = $this->findOneBy($filter);
        if (!$size) {
            throw new NotFoundException(sprintf('Size not found with product id \'%d\' and size id \'%d\' and gender \'%s\'.', $productId, $sizeId, $gender));
        }
        return $size;
    }



    /**
     * @param $id int
     * @return Size[]|array
     */
    public function findByProductId(int $id) : array
    {
        $filter['size'] = ['size', 'ASC'];
        $filter['where'][] = ['productId', '=', $id];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $productId int
     * @param $gender string
     * @param $sizeId int
     * @return Size|null
     */
    public function findOneByProductIdAndGenderAndSizeId(int $productId, string $gender, int $sizeId)
    {
        $filter['where'][] = ['productId', '=', $productId];
        $filter['where'][] = ['gender', '=', $gender];
        $filter['where'][] = ['sizeId', '=', $sizeId];
        return $this->findOneBy($filter) ?: NULL;
    }



    /**
     * @param $productId int
     * @param $gender string
     * @return float
     * @throws NotFoundException
     */
    public function getMinPriceByProductIdAndGender(int $productId, string $gender)
    {
        //todo learn mapper read column from MIN(..)
        $filter['columns'] = ['MIN(pws_price) AS minPrice'];
        $filter['where'][] = ['productId', '=', $productId];
        $filter['where'][] = ['gender', '=', $gender];
        $result = $this->getEntityMapper()
            ->getQueryManager(Size::class)
            ->findOneBy($filter, function ($row) {
                return $row['minPrice'] ?? NULL;
            });
        if (!$result) {
            throw new NotFoundException('Min price not found.');
        }
        return $result;
    }
}