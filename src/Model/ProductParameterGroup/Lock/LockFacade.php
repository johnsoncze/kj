<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup\Lock;

use App\Helpers\Entities;
use App\NotFoundException;
use App\Product\Parameter\ProductParameterRepository;
use App\ProductParameterGroup\Lock\Parameter\Parameter;
use App\ProductParameterGroup\Lock\Parameter\ParameterRepository AS LockParameterRepository;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use Kdyby\Monolog\Logger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class LockFacade
{


    /** LockRepository */
    private $lockRepo;

    /** @var LockParameterRepository */
    private $lockParameterRepo;

    /** @var Logger */
    private $logger;

    /** @var ProductParameterGroupRepository */
    private $productParameterGroupRepo;

    /** @var ProductParameterRepository */
    private $productParameterRelationRepo;



    public function __construct(LockRepository $lockRepo,
                                LockParameterRepository $lockParameterRepo,
                                Logger $logger,
                                ProductParameterGroupRepository $productParameterGroupRepository,
                                ProductParameterRepository $productParameterRepo)
    {
        $this->lockRepo = $lockRepo;
        $this->lockParameterRepo = $lockParameterRepo;
        $this->logger = $logger;
        $this->productParameterGroupRepo = $productParameterGroupRepository;
        $this->productParameterRelationRepo = $productParameterRepo;
    }



    /**
     * @param $key string
     * @param $productId array
     * @return Parameter[]|array
     */
    public function getByKeyAndMoreProductId(string $key, array $productId) : array
    {
        $result = [];

        try {
            $locks = $this->lockRepo->getByKey($key);
            $lockId = $locks ? Entities::getProperty($locks, 'id') : [];
            $lockParameters = $lockId ? $this->lockParameterRepo->findByMoreLockId($lockId) : [];
            if ($lockParameters) {
                $lockParameters = Entities::setValueAsKey($lockParameters, 'parameterId');
                $parameterId = Entities::getProperty($lockParameters, 'parameterId');
                $productParameters = $this->productParameterRelationRepo->findByProductIdAndParameterId($productId, $parameterId);
                foreach ($productParameters as $productParameter) {
                    $lockParameter = $lockParameters[$productParameter->getParameterId()];
                    /*if(!$lockParameter) {
                        throw new \Exception('Cannot find')
                    }*/
                    $setLockParameter = $result[$productParameter->getProductId()] ?? NULL;
                    if ($setLockParameter === NULL || $setLockParameter->getWeight() < $lockParameter->getWeight()) {
                        $result[$productParameter->getProductId()] = $lockParameter;
                    }
                }
            }

            //check if required products has value
            $missingProducts = array_diff($productId, array_keys($result));
            if ($missingProducts) {
                $this->logger->addNotice(sprintf('Pro produkty s id \'%s\' pro zámek \'%s\' skupiny parametrů produktu nebyl nalezen výsledek.', implode(', ', $missingProducts), $key));
            }
        } catch (NotFoundException $exception) {
            $message = sprintf('Nebyl nalezen zámek \'%s\' pro skupinu parametrů produktu.', $key);
            $this->logger->addError($message);
        }

        return $result;
    }



    /**
     * @param $key string
     * @param $productId int
     * @return array
     */
    public function findByKeyAndProductId(string $key, int $productId) : array
    {
        $parameters = [];

        try {
            $locks = $this->lockRepo->getByKey($key);
            $lockParameters = $this->lockParameterRepo->findByMoreLockId(Entities::getProperty($locks, 'id'));
            if ($lockParameters) {
                $lockParameters = Entities::setValueAsKey($lockParameters, 'parameterId');
                $parameterId = Entities::getProperty($lockParameters, 'parameterId');
                $productParameters = $this->productParameterRelationRepo->findByProductIdAndParameterId([$productId], $parameterId);
                foreach ($productParameters as $productParameter) {
                    $_parameterId = $productParameter->getParameterId();
                    $parameters[$_parameterId] = $lockParameters[$_parameterId]->getValue();
                }
            }
        } catch (NotFoundException $exception) {
            //nothing..
        }

        return $parameters;
    }



    /**
     * @param $key string
     * @param $productId int
     * @return string|null
     */
    public function findOneValueByKeyAndProductId(string $key, int $productId)
    {
        $parameters = $this->findByKeyAndProductId($key, $productId);
        if ($parameters && count($parameters) > 1) {
            $this->logger->addNotice(sprintf('Got more than 1 expected result. Product id \'%d\' with key \'%s\'.', $productId, $key), [
                'result' => print_r($parameters, TRUE),
            ]);
        }
        return $parameters ? end($parameters) : NULL;
    }



    /**
     * @param $key string
     * @return ProductParameterGroupEntity|null
     */
    public function getOneGroupByKey(string $key)
    {
        try {
            $lock = $this->lockRepo->getOneByKey($key);
            return $this->productParameterGroupRepo->getOneById($lock->getGroupId());
        } catch (NotFoundException $exception) {
            $message = sprintf('Not found lock with key \'%s\'.', $key);
            $this->logger->addNotice($message);
            return NULL;
        }
    }
}