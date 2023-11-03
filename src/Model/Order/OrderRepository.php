<?php

declare(strict_types = 1);

namespace App\Order;

use App\Extensions\Grido\IRepositorySource;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OrderRepository extends BaseRepository implements IRepositorySource
{


    /** @var string */
    protected $entityName = Order::class;



    /**
     * @param $token string
     * @return Order
     * @throws OrderNotFoundException
     */
    public function getOneByToken(string $token) : Order
    {
        $filter['where'][] = ['token', '=', $token];
        $order = $this->findOneBy($filter);
        if (!$order) {
            throw new OrderNotFoundException('Order not found.');
        }
        return $order;
    }



    /**
     * @param $token string
     * @param $transactionId string
     * @return Order
     * @throws OrderNotFoundException
     */
    public function getOneByTokenAndPaymentGatewayTransactionId(string $token, string $transactionId) : Order
    {
        $filter['where'][] = ['token', '=', $token];
        $filter['where'][] = ['paymentGatewayTransactionId', '=', $transactionId];
        $order = $this->findOneBy($filter);
        if (!$order) {
            throw new OrderNotFoundException('Order not found.');
        }
        return $order;
    }



    /**
     * @param $code string
     * @param $customerId int
     * @return Order
     * @throws OrderNotFoundException
     */
    public function getOneByCodeAndCustomerId(string $code, int $customerId) : Order
    {
        $filters['where'][] = ['code', '=', $code];
        $filters['where'][] = ['customerId', '=', $customerId];
        $order = $this->findOneBy($filters);
        if (!$order) {
            throw new OrderNotFoundException('Order not found.');
        }
        return $order;
    }



    /**
     * @param $code string
     * @return Order
     * @throws OrderNotFoundException
     */
    public function getOneNotSentToEETrackingByCode(string $code) : Order
    {
        $filters['where'][] = ['code', '=', $code];
        $filters['where'][] = ['sentToEETracking', '=', FALSE];
        $order = $this->findOneBy($filters);
        if (!$order) {
            throw new OrderNotFoundException('Order not found.');
        }
        return $order;
    }



    /**
     * @param $id int
     * @return Order
     * @throws OrderNotFoundException
     */
    public function getOneById(int $id) : Order
    {
        $filters['where'][] = ['id', '=', $id];
        $order = $this->findOneBy($filters);
        if (!$order) {
            throw new OrderNotFoundException(sprintf('Objednávka s id \'%d\' nebyla nalezena.', $id));
        }
        return $order;
    }



    /**
     * @param $state string
     * @return CountDTO
     */
    public function getCountByState(string $state) : CountDTO
    {
        $filter['where'][] = ['state', '=', $state];
        return $this->count($filter);
    }

    /**
     * @return CountDTO
     */
    public function getCount() : CountDTO
    {
        return $this->count([]);
    }

    /**
     * @param $createdDate \DateTime
     * @return Order[]|array
    */
    public function findWaitingForAcceptedStateByCreatedDate(\DateTime $createdDate) : array
    {
        $filter['where'][] = ['addDate', '<=', $createdDate->format('Y-m-d H:i:s')];
        $filter['where'][] = ['state', '=', Order::NEW_STATE];
        $filter['whereOr'] = [
            ['isRequiredPaymentGateway', '!=', TRUE],
            ['paymentGatewayTransactionState', '=', Order::PAID_PAYMENT_GATEWAY_STATE],
        ];

        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $customerId int
     * @return Order|null
     */
    public function findOneLastByCustomerId(int $customerId)
    {
        $filter['limit'] = 1;
        $filter['sort'] = ['addDate', 'DESC'];
        $filter['where'][] = ['customerId', '=', $customerId];
        return $this->findOneBy($filter) ?: NULL;
    }



    /**
     * @param $state string
     * @return Order[]|array
     */
    public function findForExportToExternalSystem(string $state = Order::ACCEPTED_STATE) : array
    {
        $filter['where'][] = ['sentToExternalSystem', '=', FALSE];
        $filter['where'][] = ['state', '=', $state];
        $filter['whereOr'] = [
            ['isRequiredPaymentGateway', '!=', TRUE],
            ['paymentGatewayTransactionState', '=', Order::PAID_PAYMENT_GATEWAY_STATE],
        ];

        return $this->findBy($filter) ?: [];
    }
}