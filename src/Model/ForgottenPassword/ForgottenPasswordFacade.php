<?php

namespace App\ForgottenPassword;

use App\BaseFacade;
use App\Customer\CustomerNotFoundException;
use App\Customer\CustomerRepository;
use App\FacadeException;
use App\NotFoundException;
use App\ServiceException;
use App\User\UserRepositoryFactory;


class ForgottenPasswordFacade extends BaseFacade
{


    /** @var CustomerRepository */
    protected $customerRepo;

    /** @var UserRepositoryFactory */
    protected $userRepositoryFactory;

    /** @var ForgottenPasswordRepositoryFactory */
    protected $forgottenPasswordRepositoryFactory;

    /** @var ForgottenPasswordEmailServiceFactory */
    protected $emailServiceFactory;

    /** @var ForgottenPasswordCheckServiceFactory */
    protected $checkServiceFactory;

    /** @var ForgottenPasswordHashServiceFactory */
    protected $hashServiceFactory;



    public function __construct(CustomerRepository $customerRepository,
                                UserRepositoryFactory $userRepositoryFactory,
                                ForgottenPasswordRepositoryFactory $forgottenPasswordRepositoryFactory,
                                ForgottenPasswordEmailServiceFactory $emailServiceFactory,
                                ForgottenPasswordCheckServiceFactory $forgottenPasswordCheckServiceFactory,
                                ForgottenPasswordHashServiceFactory $forgottenPasswordHashServiceFactory)
    {
        $this->customerRepo = $customerRepository;
        $this->userRepositoryFactory = $userRepositoryFactory;
        $this->forgottenPasswordRepositoryFactory = $forgottenPasswordRepositoryFactory;
        $this->emailServiceFactory = $emailServiceFactory;
        $this->checkServiceFactory = $forgottenPasswordCheckServiceFactory;
        $this->hashServiceFactory = $forgottenPasswordHashServiceFactory;
    }



    /**
     * Add a new request for user
     * @param $email string
     * @return void
     */
    public function addNewForUser($email)
    {
        $userRepository = $this->userRepositoryFactory->create();
        $forgottenPasswordRepository = $this->forgottenPasswordRepositoryFactory->create();
        $user = $userRepository->findOneByEmail($email);
        if ($user) {
            $requests = $forgottenPasswordRepository->findByUserId($user->getId());
            if ($requests) {
                $forgottenPasswordRepository->remove($requests);
            }
            $entity = (new ForgottenPasswordEntityFactory())->createFromUser($user);
            $this->hashServiceFactory->create()->setHash($entity);
            $forgottenPasswordRepository->save($entity);
            $this->emailServiceFactory->create()->sendNewRequestUser($entity, $email);
        }
    }



    /**
     * @param $email string
     * @return ForgottenPasswordEntity
     * @throws ForgottenPasswordFacadeException
     * todo test
     */
    public function addRequestForCustomer(string $email) : ForgottenPasswordEntity
    {
        $forgottenPasswordRepository = $this->forgottenPasswordRepositoryFactory->create();

        try {
            $customer = $this->customerRepo->getOneAllowedByEmail($email);
            if ($customer->isActivated()) {
                $requests = $forgottenPasswordRepository->findByCustomerId($customer->getId());
                if ($requests) {
                    $forgottenPasswordRepository->remove($requests);
                }
                $entity = (new ForgottenPasswordEntityFactory())->createFromCustomer($customer);
                $this->hashServiceFactory->create()->setHash($entity);
                $forgottenPasswordRepository->save($entity);
                $this->emailServiceFactory->create()->sendRequestToCustomer($entity, $customer);
                return $entity;
            }
            throw new ForgottenPasswordFacadeException('Customer is not activated yet.', ForgottenPasswordFacadeException::NOT_ACTIVATED);
        } catch (CustomerNotFoundException $exception) {
            throw new ForgottenPasswordFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $userId int
     * @param $hash string
     * @return ForgottenPasswordEntity
     * @throws FacadeException
     * todo test
     */
    public function getUserValidRequest($userId, $hash)
    {
        try {
            $request = $this->forgottenPasswordRepositoryFactory->create()
                ->getOneByUserIdAndHash($userId, $hash);
            $this->checkServiceFactory->create()->checkValidityDate($request);
            return $request;
        } catch (NotFoundException $exception) {
            throw new FacadeException("Neplatný požadavek.");
        } catch (ServiceException $exception) {
            throw new FacadeException($exception->getMessage());
        }
    }



    /**
     * @param $email string
     * @param $hash string
     * @return ForgottenPasswordEntity
     * @throws FacadeException
     * todo test
     */
    public function validateCustomerRequest(string $email, string $hash) : ForgottenPasswordEntity
    {
        try {
            $customer = $this->customerRepo->getOneAllowedByEmail($email);
            $request = $this->forgottenPasswordRepositoryFactory->create()->getOneByCustomerIdAndHash($customer->getId(), $hash);
            $this->checkServiceFactory->create()->checkValidityDate($request);
            return $request;
        } catch (NotFoundException $exception) {
            throw new FacadeException("Neplatný požadavek.");
        } catch (ServiceException $exception) {
            throw new FacadeException($exception->getMessage());
        } catch (CustomerNotFoundException $exception) {
            throw new FacadeException($exception->getMessage());
        }
    }



    /**
     * Remove user requests
     * @param $userId int
     * @return void
     */
    public function removeUserRequests($userId)
    {
        $repository = $this->forgottenPasswordRepositoryFactory->create();
        if ($requests = $repository->findByUserId($userId)) {
            $repository->remove($requests);
        }
    }



    /**
     * Remove customer requests
     * @param $customerId int
     * todo test
     */
    public function removeCustomerRequests(int $customerId)
    {
        $repository = $this->forgottenPasswordRepositoryFactory->create();
        $requests = $repository->findByCustomerId($customerId);
        if ($requests) {
            $repository->remove($requests);
        }
    }

}