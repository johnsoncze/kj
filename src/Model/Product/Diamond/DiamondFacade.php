<?php

declare(strict_types = 1);

namespace App\Product\Diamond;

use App\Diamond\DiamondRepository;
use App\Diamond\Price\PriceRepository;
use App\Environment\Environment;
use App\Helpers\Entities;
use App\NotFoundException;
use App\Product\Diamond\DiamondRepository As ProductDiamondRepository;
use App\Product\ProductNotFoundException;
use App\Product\ProductRepository;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockRepository;
use App\ProductParameterGroup\Lock\Parameter\ParameterRepository;
use Kdyby\Monolog\Logger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class DiamondFacade
{


    /** @var PriceRepository */
    private $diamondPriceRepo;

    /** @var DiamondRepository */
    private $diamondRepo;

    /** @var ParameterRepository */
    private $lockGroupParameterRepo;

    /** @var LockRepository */
    private $lockGroupRepo;

    /** @var Logger */
    private $logger;

    /** @var ProductDiamondRepository */
    private $productDiamondRepo;

    /** @var ProductRepository */
    private $productRepo;



    public function __construct(DiamondRepository $diamondRepo,
                                ParameterRepository $parameterRepository,
                                LockRepository $lockRepository,
                                Logger $logger,
                                PriceRepository $priceRepository,
                                ProductDiamondRepository $productDiamondRepository,
                                ProductRepository $productRepository)
    {
        $this->diamondRepo = $diamondRepo;
        $this->diamondPriceRepo = $priceRepository;
        $this->lockGroupParameterRepo = $parameterRepository;
        $this->lockGroupRepo = $lockRepository;
        $this->logger = $logger;
        $this->productDiamondRepo = $productDiamondRepository;
        $this->productRepo = $productRepository;
    }



    /**
     * @param $productId int
     * @param $diamondId int
     * @param $gender string|null
     * @param $quantity int
     * @return Diamond
     * @throws DiamondFacadeException
     */
    public function save(int $productId, int $diamondId, string $gender = NULL, int $quantity) : Diamond
    {
        try {
            $diamond = $this->diamondRepo->getOneById($diamondId);
            $product = $this->productRepo->getOneById($productId);

            $productDiamond = $this->productDiamondRepo->findOneByProductIdAndDiamondIdAndGender($productId, $diamondId, $gender) ?: new Diamond();
            $productDiamond->setProductId($product->getId());
            $productDiamond->setDiamondId($diamond->getId());
            $productDiamond->setGender($gender);
            $productDiamond->setQuantity($quantity);
            $this->productDiamondRepo->save($productDiamond);

            return $productDiamond;
        } catch (\EntityInvalidArgumentException $exception) {
            throw new DiamondFacadeException($exception->getMessage());
        } catch (NotFoundException $exception) {
            throw new DiamondFacadeException($exception->getMessage());
        } catch (DiamondDuplicationException $exception) {
            throw new DiamondFacadeException($exception->getMessage());
        } catch (ProductNotFoundException $exception) {
            throw new DiamondFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $id int
     * @return bool
     * @throws DiamondFacadeException
     */
    public function remove(int $id) : bool
    {
        try {
            $diamond = $this->productDiamondRepo->getOneById($id);
            $this->productDiamondRepo->remove($diamond);
            return TRUE;
        } catch (NotFoundException $exception) {
            throw new DiamondFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $productId int
     * @return array
     */
    public function findDiamondQualitiesByProductId(int $productId) : array
    {
        $list = [];
        $key = Lock::DIAMOND_CALCULATOR;

        try {
            $productDiamonds = $this->productDiamondRepo->findByProductId($productId);
            if ($productDiamonds) {
                $qualityId = $this->diamondPriceRepo->findQualityIdByMoreDiamondId(Entities::getProperty($productDiamonds, 'diamondId'));
                if ($qualityId) {
                    $group = $this->lockGroupRepo->getOneByKey($key);
                    $list = $this->lockGroupParameterRepo->findByLockIdAndMoreParameterId($group->getId(), $qualityId);
                }
            }
        } catch (NotFoundException $exception) {
            $this->logger->addError(sprintf('Nebyl nalezen zámek \'%s\' pro skupinu parametrů produktu.', $key));
        }

        if (count($list) < 2) {
            $this->logger->addNotice(sprintf('Pro produkt s id \'%d\' nebyla nalezena žádná kvalita diamantů či méně než dva.', $productId));
        }

        return $list;
    }



    /**
     * @param $id int
     * @param $gender string
     * @return Diamond[]|array
    */
    public function findByProductIdAndGender(int $id, string $gender) : array
    {
        return $this->productDiamondRepo->findByProductIdAndGender($id, $gender);
    }
}