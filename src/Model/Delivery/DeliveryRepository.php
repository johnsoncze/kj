<?php

declare(strict_types = 1);

namespace App\Delivery;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class DeliveryRepository extends BaseRepository implements IRepositorySource, IRepository
{


    /** @var string */
    protected $entityName = Delivery::class;

    /**
     * @param $parameterId array
     * @return array
     */
    public function findProductIdByMoreParameterIdAsCategoryParameter(array $parameterId) : array
    {
        $productParameterRelation = ProductParameter::getAnnotation();
        $subQuery = sprintf('(SELECT %s FROM %s WHERE %s IN (\'%s\') GROUP BY %1$s HAVING COUNT(*) = \'%d\')',
            $productParameterRelation->getPropertyByName('productId')->getColumn()->getName(),
            $productParameterRelation->getTable()->getName(),
            $productParameterRelation->getPropertyByName('parameterId')->getColumn()->getName(),
            implode('\',\'', $parameterId),
            count($parameterId));

        $filter['columns'] = ['id'];
        $filter['where'][] = ['id', 'IN.SQL', $subQuery];

        $result = $this->getEntityMapper()
            ->getQueryManager(Product::class)
            ->findBy($filter, function ($rows) {
                $response = [];
                $productAnnotation = Product::getAnnotation();
                $columnId = $productAnnotation->getPropertyByName('id');
                foreach ($rows as $row) {
                    $id = $row[$columnId->getColumn()->getName()];
                    $response[$id] = $id;
                }
                return $response;
            });
        return $result ?: [];
    }


}