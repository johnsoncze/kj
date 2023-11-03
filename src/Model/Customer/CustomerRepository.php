<?php

declare(strict_types = 1);

namespace App\Customer;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CustomerRepository extends BaseRepository implements IRepositorySource, IRepository
{


    /** @var string */
    protected $entityName = Customer::class;



    /**
     * @param int $id
     * @return Customer
     * @throws CustomerNotFoundException
     */
    public function getOneById(int $id) : Customer
    {
        $customer = $this->findOneBy([
            'where' => [
                ['id', '=', $id],
            ]
        ]);
        if (!$customer) {
            throw new CustomerNotFoundException('Customer not found.');
        }
        return $customer;
    }



    /**
     * @param $email string
     * @return Customer
     * @throws CustomerNotFoundException on customer not found
     */
    public function getOneAllowedByEmail(string $email) : Customer
    {
        $filters['where'][] = ['email', '=', $email];
        $filters['where'][] = ['state', '=', Customer::ALLOWED];
        $customer = $this->findOneBy($filters);
        if (!$customer) {
            throw new CustomerNotFoundException('Customer not found.');
        }
        return $customer;
    }



    /**
     * @param int $id
     * @return Customer
     * @throws CustomerNotFoundException
     */
    public function getOneAllowedById(int $id) : Customer
    {
        $customer = $this->findOneBy([
            'where' => [
                ['id', '=', $id],
                ['state', '=', Customer::ALLOWED]
            ]
        ]);
        if (!$customer) {
            throw new CustomerNotFoundException('Customer not found.');
        }
        return $customer;
    }



    /**
     * @param $email string
     * @param $token string
     * @return Customer
     * @throws CustomerNotFoundException
     */
    public function getOneAllowedByEmailAndActivationToken(string $email, string $token) : Customer
    {
        $filters['where'][] = ['email', '=', $email];
        $filters['where'][] = ['activationToken', '=', $token];
        $filters['where'][] = ['state', '=', Customer::ALLOWED];
        $customer = $this->findOneBy($filters);
        if (!$customer) {
            throw new CustomerNotFoundException('Customer not found.');
        }
        return $customer;
    }



    /**
     * @param $email string
     * @return Customer|null
     */
    public function findOneByEmail(string $email)
    {
        $filters['where'][] = ['email', '=', $email];
        return $this->findOneBy($filters) ?: NULL;
    }



    /**
     * @param $id int
     * @return Customer|null
     */
    public function findOneByExternalSystemId(int $id)
    {
        $filters['where'][] = ['externalSystemId', '=', $id];
        return $this->findOneBy($filters) ?: NULL;
    }



    /**
     * Find external system id list.
     * @return array
     */
    public function findExternalSystemIdList() : array
    {
        $mapper = function ($rows) {
            $list = [];
            foreach ($rows as $row) {
                $list[$row['cus_external_system_id']]['id'] = $row['cus_id'];
                $list[$row['cus_external_system_id']]['externalSystemLastChangeDate'] = $row['cus_external_system_last_change_date'];
            }
            return $list;
        };

        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy([], $mapper) ?: [];
    }



    /**
     * Find emails for activation.
     * @return array
     * todo napsat test
     */
    public function findEmailsForActivation() : array
    {
        $filters['where'][] = ['password', '', NULL];
        $filters['where'][] = ['activationToken', '', NULL];

        //find emails after launch of project
        //because database contains old customers
        //who never activated in the previous online store
        $filters['where'][] = ['addDate', '>=', '2019-06-19'];

        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy($filters, function ($rows) {
                $list = [];
                foreach ($rows as $row) {
                    $list[] = $row['cus_email'];
                }
                return $list;
            }) ?: [];
    }



    /**
     * @return Customer[]|array
    */
    public function findWithoutExternalSystemId() : array
    {
        $filters['where'][] = ['externalSystemId', '', NULL];
        return $this->findBy($filters) ?: [];
    }



    /**
     * @param $dateTime \DateTime
     * @return Customer[]|array
    */
    public function findUpdatedFromDate(\DateTime $dateTime) : array
    {
        $filters['where'][] = ['updateDate', '>=', $dateTime->format('Y-m-d H:i:s')];
        return $this->findBy($filters) ?: [];
    }
}