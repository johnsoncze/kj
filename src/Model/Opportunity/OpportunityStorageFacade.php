<?php

declare(strict_types = 1);

namespace App\Opportunity;

use App\Customer\CustomerNotFoundException;
use App\Customer\CustomerRepository;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OpportunityStorageFacade
{


    /** @var CustomerRepository */
    private $customerRepo;

    /** @var OpportunityCode */
    private $opportunityCode;

    /** @var OpportunityRepository */
    private $opportunityRepo;

    /** @var ITranslator */
    private $translator;



    public function __construct(CustomerRepository $customerRepository,
                                ITranslator $translator,
                                OpportunityCode $opportunityCode,
                                OpportunityRepository $opportunityRepo)
    {
        $this->customerRepo = $customerRepository;
        $this->opportunityCode = $opportunityCode;
        $this->opportunityRepo = $opportunityRepo;
        $this->translator = $translator;
    }



    /**
     * Add an new opportunity.
     * @param $customerId int|null
     * @param $firstName string
     * @param $lastName string
     * @param $preferredContact string
     * @param $email string|null
     * @param $telephone string|null
     * @param $requestDate string|null
     * @param $comment string|null
     * @param $page string|null
     * @param $pageId int|null
     * @param $type string
     * @param $pageArguments array
     * @return Opportunity
     * @throws OpportunityStorageFacadeException
     */
    public function add(int $customerId = NULL,
                        string $firstName,
                        string $lastName,
                        string $preferredContact,
                        string $email = NULL,
                        string $telephone = NULL,
                        string $requestDate = NULL,
                        string $comment = NULL,
                        string $page,
                        int $pageId = NULL,
                        string $type,
                        array $pageArguments = []) : Opportunity
    {
        try {
            $customer = $customerId !== NULL ? $this->customerRepo->getOneAllowedById($customerId) : NULL;
        } catch (CustomerNotFoundException $exception) {
            //nothing..
        }

        try {
            $opportunity = new Opportunity();
            $opportunity->setCode($this->opportunityCode->generate());
            $opportunity->setCustomerId(isset($customer) ? $customer->getId() : NULL);
            $opportunity->setFirstName($firstName);
            $opportunity->setLastName($lastName);
            $opportunity->setPreferredContact($preferredContact);
            $opportunity->setEmail($email, $this->translator);
            $opportunity->setTelephone($telephone);
            $opportunity->setRequestDate($requestDate);
            $opportunity->setComment($comment);
            $opportunity->setPage($page);
            $opportunity->setPageId($pageId);
            $opportunity->setPageArguments($pageArguments);
            $opportunity->setType($type);
            $opportunity->setState(Opportunity::STATE_NEW);

            $this->opportunityRepo->save($opportunity);

            return $opportunity;
        } catch (\EntityInvalidArgumentException $exception) {
            throw new OpportunityStorageFacadeException($exception->getMessage());
        }
    }



    /**
     * Change state.
     * @param $opportunityId int
     * @param $state string
     * @return Opportunity
     * @throws OpportunityStorageFacadeException some error
     * todo test
     */
    public function changeState(int $opportunityId, string $state) : Opportunity
    {
        try {
            $opportunity = $this->opportunityRepo->getOneById($opportunityId);
            $opportunity->setState($state);
            $this->opportunityRepo->save($opportunity);
            return $opportunity;
        } catch (OpportunityNotFoundException $exception) {
            throw new OpportunityStorageFacadeException($exception->getMessage());
        }
    }
}