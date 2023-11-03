<?php

declare(strict_types = 1);

namespace App\Customer;

use App\Password\PasswordService;
use App\Password\PasswordServiceException;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CustomerStorageFacade
{


    /** @var CustomerDuplication */
    private $customerDuplication;

    /** @var CustomerRepository */
    private $customerRepo;

    /** @var PasswordService */
    private $passwordService;

    /** @var EmailSender */
    private $emailSender;

    /** @var ITranslator */
    private $translator;



    public function __construct(CustomerDuplication $customerDuplication,
                                CustomerRepository $customerRepository,
                                EmailSender $emailSender,
                                ITranslator $translator,
                                PasswordService $passwordService)
    {
        $this->customerDuplication = $customerDuplication;
        $this->customerRepo = $customerRepository;
        $this->emailSender = $emailSender;
        $this->passwordService = $passwordService;
        $this->translator = $translator;
    }


    /**
     * Add a new customer.
     * @param $id int|null
     * @param $email string
     * @param $firstName string
     * @param $lastName string
     * @param $sex string|null
     * @param $externalSystemId int|null
     * @param $password string|null
     * @param $telephone string|null
     * @param $addressing string|null
     * @param $street string|null
     * @param $city string|null
     * @param $postcode int|null
     * @param $countryCode string|null
     * @param $birthdayYear int|null
     * @param $birthdayMonth int|null
     * @param $birthdayDay int|null
     * @param $birthdayCoupon bool
     * @param $newsletter bool
     * @param $externalSystemLastChangeDate string|null
     * @param $state string
     * @param string|null $code
     * @return Customer
     * @throws CustomerStorageException
     * @throws \Ricaefeliz\Mappero\Exceptions\EntityException
     */
    public function save(int $id = NULL,
                         string $email,
                         string $firstName,
                         string $lastName,
                         string $sex = NULL,
                         int $externalSystemId = NULL,
                         string $password = NULL,
                         string $telephone = NULL,
                         string $addressing = NULL,
                         string $street = NULL,
                         string $city = NULL,
                         int $postcode = NULL,
                         string $countryCode = NULL,
                         int $birthdayYear = NULL,
                         int $birthdayMonth = NULL,
                         int $birthdayDay = NULL,
                         bool $birthdayCoupon = FALSE,
                         bool $newsletter = FALSE,
                         string $externalSystemLastChangeDate = NULL,
                         string $state = Customer::ALLOWED,
                         string $code = NULL)
    {
        try {
            $customer = $id !== NULL ? $this->customerRepo->getOneById($id) : new Customer();
            $customer->setEmail($email, $this->translator);
            $customer->setFirstName($firstName);
            $customer->setLastName($lastName);
            $customer->setSex($sex);
            $customer->setExternalSystemId($externalSystemId);
            $password && $customer->setPassword($password);
            $customer->setTelephone($telephone);
            $customer->setAddressing($addressing);
            $customer->setStreet($street);
            $customer->setCity($city);
            $customer->setPostcode($postcode);
            $customer->setCountryCode($countryCode);
            $customer->setBirthdayYear($birthdayYear);
            $customer->setBirthdayMonth($birthdayMonth);
            $customer->setBirthdayDay($birthdayDay);
            $customer->setBirthdayCoupon($birthdayCoupon);
            $customer->setNewsletter($newsletter);
            $customer->setExternalSystemLastChangeDate($externalSystemLastChangeDate);
            $customer->setState($state);
            $customer->setCode($code);
            $customer->setAddDate(new \DateTime());
						
						$customer->setBirthdayCoupon($customer->shouldHaveBirthDayCoupon());
						
            $this->checkDuplication($customer);
            $this->customerRepo->save($customer);

            return $customer;
        } catch (CustomerDuplicationException $exception) {
            throw new CustomerStorageException($exception->getMessage());
        } catch (CustomerNotFoundException $exception) {
            throw new CustomerStorageException($exception->getMessage());
        } catch (\EntityInvalidArgumentException $exception) {
            throw new CustomerStorageException($exception->getMessage());
        }
    }



    /**
     * Add a new customer from registration form.
     * @param $name string
     * @param $surname string
     * @param $email string
     * @param $password string
     * @param $sex string
     * @param $birthdayYear int|null
     * @param $birthdayMonth int|null
     * @param $birthdayDay int|null
     * @param $street string|null
     * @param $city string|null
     * @param $postcode int|null
     * @param $countryCode string|null
     * @param $telephone string|null
     * @param $hearAboutUs string|null
     * @param $hearAboutUsComment string|null
     * @param $newsletter bool
     * @return Customer
     * @throws CustomerStorageException
     * todo test
     */
    public function addFromRegistrationForm(string $name,
                                            string $surname,
                                            string $email,
                                            string $password,
                                            string $sex,
                                            int $birthdayYear = NULL,
                                            int $birthdayMonth = NULL,
                                            int $birthdayDay = NULL,
                                            string $street = NULL,
                                            string $city = NULL,
                                            int $postcode = NULL,
                                            string $countryCode = NULL,
                                            string $telephone = NULL,
                                            string $hearAboutUs = NULL,
                                            string $hearAboutUsComment = NULL,
                                            bool $newsletter = FALSE) : Customer
    {
        try {
            $customer = new Customer();
            $customer->setFirstName($name);
            $customer->setLastName($surname);
            $customer->setEmail($email, $this->translator);
            $customer->setPassword($this->passwordService->hash($customer, $password));
            $customer->setSex($sex);
            $customer->setBirthdayYear($birthdayYear);
            $customer->setBirthdayMonth($birthdayMonth);
            $customer->setBirthdayDay($birthdayDay);
            $customer->setBirthdayCoupon($birthdayMonth !== NULL && (int)$birthdayMonth === (int)(new \DateTime())->format('n'));
            $customer->setStreet($street);
            $customer->setCity($city);
            $customer->setPostcode($postcode);
            $customer->setCountryCode($countryCode);
            $customer->setTelephone($telephone);
            $customer->setHearAboutUs($hearAboutUs);
            $customer->setHearAboutUsComment($hearAboutUsComment);
            $customer->setNewsletter($newsletter);
            $customer->setAddDate(new \DateTime());
            $customer->setState(Customer::ALLOWED);
            $customer->setActivationDate((new \DateTime())->format('Y-m-d H:i:s'));

						$customer->setBirthdayCoupon($customer->shouldHaveBirthDayCoupon());
						
            $this->checkDuplication($customer);
            $this->customerRepo->save($customer);
            $this->emailSender->sendSuccessfulRegistration($customer);

            return $customer;
        } catch (CustomerDuplicationException $exception) {
            throw new CustomerStorageException($exception->getMessage());
        } catch (\EntityInvalidArgumentException $exception) {
            throw new CustomerStorageException($exception->getMessage());
        }
    }



    /**
     * Change password.
     * @param $customerId int
     * @param $actual string
     * @param $new string
     * @return Customer
     * @throws CustomerStorageException
     * @throws \InvalidArgumentException
     * todo test
     */
    public function changePassword(int $customerId, string $actual, string $new) : Customer
    {
        try {
            $customer = $this->customerRepo->getOneAllowedById($customerId);
            if (!$customer->isActivated()) {
                throw new \InvalidArgumentException('Password can not be changed, because user is not activated yet.');
            }
            $this->passwordService->verify($customer, $actual);
            $customer->setPassword($this->passwordService->hash($customer, $new));
            $this->customerRepo->save($customer);
            return $customer;
        } catch (CustomerNotFoundException $exception) {
            throw new CustomerStorageException($exception->getMessage());
        } catch (PasswordServiceException $exception) {
            throw new CustomerStorageException($this->translator->translate('form.newPassword.message.error.invalidActualPassword'));
        }
    }



    /**
     * Set password.
     * @param $customerId int
     * @param $password string
     * @return Customer
     * @throws CustomerStorageException
     * todo test
     */
    public function setPassword(int $customerId,
                                string $password) : Customer
    {
        try {
            $customer = $this->customerRepo->getOneAllowedById($customerId);
            $customer->setPassword($this->passwordService->hash($customer, $password));
            $this->customerRepo->save($customer);
            return $customer;
        } catch (CustomerNotFoundException $exception) {
            throw new CustomerStorageException($exception->getMessage());
        }
    }



    /**
     * Set subscription of newsletter.
     * @param $customerId int
     * @param $subscription bool
     * @return Customer
     * @throws CustomerStorageException
     * todo test
     */
    public function setNewsletter(int $customerId,
                                  bool $subscription) : Customer
    {
        try {
            $customer = $this->customerRepo->getOneAllowedById($customerId);
            $customer->setNewsletter($subscription);
            $this->customerRepo->save($customer);

            return $customer;
        } catch (CustomerNotFoundException $exception) {
            throw new CustomerStorageException($exception->getMessage());
        }
    }

		
		
    /**
     * Set use of birthday dscount
     * @param $customerId int
     * @return Customer
     * @throws CustomerStorageException
     * todo test
     */
    public function setBirthdayDiscountUse(int $customerId) : Customer
    {
        try {
            $customer = $this->customerRepo->getOneAllowedById($customerId);
						$customer->setBirthdayCoupon(false);
						$customer->setBirthdayCouponLastUse(date('Y-m-d H:i:s'));
            $this->customerRepo->save($customer);

            return $customer;
        } catch (CustomerNotFoundException $exception) {
            throw new CustomerStorageException($exception->getMessage());
        }
    }
		


    /**
     * Check duplication of customer.
     * @param $customer Customer
     * @return Customer
     * @throws CustomerDuplicationException
     */
    private function checkDuplication(Customer $customer) : Customer
    {
        $this->customerDuplication->checkByEmail($customer);
        $this->customerDuplication->checkByExternalSystemId($customer);
        return $customer;
    }
}