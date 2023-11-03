<?php

namespace App\Extensions\Nette;

use App\Customer\CustomerNotFoundException;
use App\Customer\CustomerRepository;
use App\FrontModule\Presenters\AbstractPresenter;
use App\NotFoundException;
use App\User\UserIdentityService;
use App\User\UserRepositoryFactory;
use Nette\Http\Session;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class UserStorage extends \Nette\Http\UserStorage
{


    /** @var CustomerRepository */
    protected $customerRepo;

    /** @var UserRepositoryFactory */
    protected $userRepositoryFactory;

    /** @var array */
    protected $load = [
        "user" => false,
        "customer" => false
    ];



    public function __construct(CustomerRepository $customerRepository,
                                Session $sessionHandler,
                                UserRepositoryFactory $userRepositoryFactory)
    {
        parent::__construct($sessionHandler);
        $this->customerRepo = $customerRepository;
        $this->userRepositoryFactory = $userRepositoryFactory;
    }



    /**
     * Returns current user identity, if any.
     * Check identity over each refresh page.
     * @return \Nette\Security\IIdentity|NULL
     */
    public function getIdentity()
    {
        $identity = parent::getIdentity();
        try {
            if ($identity && $this->getNamespace() == UserIdentityService::NAMESPACE_USER_STORAGE && $this->load["user"] === FALSE) {
                $user = $this->userRepositoryFactory->create()->getOneById($identity->getId());
                $this->load["user"] = TRUE;
                $identity->setEntity($user);
            }
            if ($identity && $this->getNamespace() === AbstractPresenter::USER_IDENTITY_NAMESPACE && $this->load['customer'] === FALSE) {
                $customer = $this->customerRepo->getOneAllowedById($identity->getId());
                $this->load["customer"] = TRUE;
                $identity->setEntity($customer);
            }
            return $identity;
        } catch (NotFoundException $exception) {
            //nothing..
        } catch (CustomerNotFoundException $exception) {
            //nothing..
        }
        return null;
    }
}