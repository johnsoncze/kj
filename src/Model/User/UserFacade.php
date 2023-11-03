<?php

namespace App\User;

use App\AdminModule\Presenters\AdminModulePresenter;
use App\Extensions\Nette\UserIdentity;
use App\FacadeException;
use App\NotFoundException;
use App\ServiceException;
use App\NObject;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\User;
use Ricaefeliz\Mappero\Exceptions\RepositoryException;


class UserFacade extends NObject
{


    /** @var UserRepositoryFactory */
    protected $userRepositoryFactory;

    /** @var UserCheckServiceFactory */
    protected $userCheckServiceFactory;

    /** @var UserPasswordServiceFactory */
    protected $userPasswordServiceFactory;

    /** @var UserIdentityServiceFactory */
    protected $userIdentityServiceFactory;



    public function __construct(UserRepositoryFactory $userRepositoryFactory,
                                UserCheckServiceFactory $userCheckServiceFactory,
                                UserPasswordServiceFactory $userPasswordServiceFactory,
                                UserIdentityServiceFactory $userIdentityServiceFactory)
    {
        $this->userRepositoryFactory = $userRepositoryFactory;
        $this->userCheckServiceFactory = $userCheckServiceFactory;
        $this->userPasswordServiceFactory = $userPasswordServiceFactory;
        $this->userIdentityServiceFactory = $userIdentityServiceFactory;
    }



    /**
     * Add a new user
     * @param $name string
     * @param $email string
     * @param $password string
	 * @param $role string
     * @return UserEntity
     * @throws FacadeException
     */
    public function addNew(string $name, string $email, string $password, string $role)
    {
        try {
            $userRepository = $this->userRepositoryFactory->create();
            $user = $userRepository->findOneByEmail($email);
            $this->userCheckServiceFactory
                ->create()
                ->checkDuplicate($user);
            $userEntity = new UserEntity();
            $userEntity->setName($name);
            $userEntity->setEmail($email);
            $userEntity->setRole($role);
            $this->userPasswordServiceFactory->create()->setPassword($userEntity, $password);
            $userRepository->save($userEntity);
            return $userEntity;
        } catch (ServiceException $exception) {
            throw new FacadeException($exception->getMessage());
        } catch (\InvalidArgumentException $exception) {
			throw new FacadeException($exception->getMessage());
		}
    }



    /**
     * Save exists user
     * @param $userEntity UserEntity
     * @param $newPassword string|null
     * @return UserEntity
     * @throws FacadeException
     */
    public function save(UserEntity $userEntity, $newPassword = null)
    {
        if (!$userEntity->getId()) {
            throw new FacadeException("For save a new user you must use method addNew().");
        }
        if ($newPassword) {
            $this->userPasswordServiceFactory->create()->setPassword($userEntity, $newPassword);
        }
        $this->userRepositoryFactory->create()->save($userEntity);
        return $userEntity;
    }



    /**
     * Save a new password
     * @param $userId int
     * @param $newPassword string
     * @param $actualPassword string|null
     * @return UserEntity
     * @throws FacadeException
     */
    public function saveNewPassword($userId, $newPassword, $actualPassword = null)
    {
        if (!$userId || !$newPassword) {
            throw new FacadeException("Missing argument.");
        }
        try {
            $userRepository = $this->userRepositoryFactory->create();
            $user = $userRepository->getOneById($userId);
            if ($actualPassword !== null && !Passwords::verify($actualPassword, $user->getPassword())) {
                throw new FacadeException("Neplatné aktuální heslo.");
            }
            $this->userPasswordServiceFactory->create()->setPassword($user, $newPassword);
            $userRepository->save($user);
            return $user;
        } catch (NotFoundException $exception) {
            throw new FacadeException("Nové heslo se nepodařilo uložit. Uživatel neexistuje.");
        }
    }



    /**
     * Remove exists user
     * @param $userId int
     * @return int
     * @throws FacadeException
     */
    public function remove($userId)
    {
        try {
            $userRepository = $this->userRepositoryFactory->create();
            $user = $userRepository->getOneById($userId);
            return $userRepository->remove($user);
        } catch (RepositoryException $exception) {
            throw new FacadeException($exception->getMessage());
        } catch (NotFoundException $exception) {
            throw new FacadeException($exception->getMessage());
        }
    }



    /**
     * Login user into administration
     * @param $email string
     * @param $password string
     * @param $user User
     * @return User
     * @throws FacadeException
     */
    public function login($email, $password, User $user)
    {
        if (!$email) {
            throw new FacadeException("Chybí e-mail pro přihlášení.");
        } elseif (!$password) {
            throw new FacadeException("Chybí heslo pro přihlášení.");
        }
        try {
            $userEntity = $this->userRepositoryFactory->create()->getOneByEmail($email);
            if (!Passwords::verify($password, $userEntity->getPassword())) {
                throw new FacadeException("Přihlášení se nezdařilo. Chybný e-mail nebo heslo.");
            }
            $identity = new UserIdentity();
            $identity->setId($userEntity->getId());
            $identity->setEntity($userEntity);
            $user->getStorage()->setNamespace(AdminModulePresenter::USER_IDENTITY_NAMESPACE);
            $user->login($identity);
            return $user;
        } catch (NotFoundException $exception) {
            throw new FacadeException("Přihlášení se nezdařilo. Chybný e-mail nebo heslo.");
        }
    }



    /**
     * Logout user from administration
     * @param $user User
     * @return User
     */
    public function logout(User $user)
    {
        $this->userIdentityServiceFactory->create()->removeIdentity($user);
        return $user;
    }



    /**
     * @param $user User
     * @return IIdentity|null
     * @throws FacadeException
     */
    public function getUserLoggedIdentity(User $user)
    {
        $identity = $this->userIdentityServiceFactory
            ->create()
            ->getIdentity($user);
        if (!$identity) {
            throw new FacadeException("Uživatel není přihlášen.");
        }
        return $identity;
    }
}