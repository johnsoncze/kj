<?php

declare(strict_types = 1);

namespace App\Customer;

use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CustomerDuplication
{


    /** @var CustomerRepository */
    protected $customerRepo;

    /** @var ITranslator */
    protected $translator;



    public function __construct(CustomerRepository $customerRepository,
                                ITranslator $translator)
    {
        $this->customerRepo = $customerRepository;
        $this->translator = $translator;
    }



    /**
     * Check if exist some another customer with same email.
     * @param $customer Customer
     * @return Customer
     * @throws CustomerDuplicationException
     */
    public function checkByEmail(Customer $customer) : Customer
    {
        $duplicateCustomer = $this->customerRepo->findOneByEmail($customer->getEmail());
        if ($duplicateCustomer instanceof Customer && (int)$customer->getId() !== (int)$duplicateCustomer->getId()) {
            throw new CustomerDuplicationException($this->translator->translate('customer.email.exists', ['email' => $customer->getEmail()]));
        }
        return $customer;
    }



    /**
     * Check if exist some another customer with same external system id.
     * @param $customer Customer
     * @return Customer
     * @throws CustomerDuplicationException
     */
    public function checkByExternalSystemId(Customer $customer) : Customer
    {
        $externalSystemId = $customer->getExternalSystemId();
        if ($externalSystemId) {
            $duplicateCustomer = $this->customerRepo->findOneByExternalSystemId($externalSystemId);
            if ($duplicateCustomer instanceof Customer && (int)$customer->getId() !== (int)$duplicateCustomer->getId()) {
                throw new CustomerDuplicationException($this->translator->translate('customer.externalsystemid.exists', ['id' => $externalSystemId]));
            }
        }
        return $customer;
    }
}