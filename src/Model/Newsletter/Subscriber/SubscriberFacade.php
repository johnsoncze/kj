<?php

declare(strict_types = 1);

namespace App\Newsletter\Subscriber;

use App\Customer\CustomerRepository;
use App\Newsletter\SubscriberFacadeException;
use App\Newsletter\SubscriberNotFoundException;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SubscriberFacade
{


    /** @var EmailSender */
    private $confirmEmailSender;

    /** @var CustomerRepository */
    private $customerRepo;

    /** @var SubscriberRepository */
    private $subscriberRepo;

    /** @var ITranslator */
    private $translator;



    public function __construct(CustomerRepository $customerRepository,
                                EmailSender $emailSender,
                                ITranslator $translator,
                                SubscriberRepository $subscriberRepository)
    {
        $this->confirmEmailSender = $emailSender;
        $this->customerRepo = $customerRepository;
        $this->subscriberRepo = $subscriberRepository;
        $this->translator = $translator;
    }



    /**
     * Add a new subscriber.
     * @param $email string
     * @return Subscriber
     * @throws SubscriberFacadeException
     * todo test
     */
    public function add(string $email) : Subscriber
    {
        try {
            $subscriber = $this->subscriberRepo->findOneByEmail($email) ?: $this->createSubscriber($email);
            if ($subscriber->isConfirmed() !== TRUE) {
                if ($subscriber->getId() === NULL) {
                    $this->subscriberRepo->save($subscriber);
                }
                $this->confirmEmailSender->sendRequest($subscriber);
            } else {
                throw new SubscriberFacadeException($this->translator->translate('form.newsletter.subscription.message.existsAlready', ['email' => $email]));
            }
            return $subscriber;
        } catch (\EntityInvalidArgumentException $exception) {
            throw new SubscriberFacadeException($exception->getMessage());
        }
    }



    /**
     * Confirm subscriber.
     * @param $email string
     * @param $token string
     * @return Subscriber
     * @throws SubscriberFacadeException
     * todo test
     */
    public function confirm(string $email, string $token) : Subscriber
    {
        try {
            $subscriber = $this->subscriberRepo->getOneNoConfirmedByEmailAndConfirmToken($email, $token);
            $subscriber->setConfirmToken(NULL);
            $subscriber->setConfirmed(TRUE);
            $this->subscriberRepo->save($subscriber);

            if (($customer = $this->customerRepo->findOneByEmail($email)) && $customer->wantNewsletter() !== TRUE){
                $customer->setNewsletter(TRUE);
                $this->customerRepo->save($customer);
            }

            return $subscriber;
        } catch (SubscriberNotFoundException $exception) {
            throw new SubscriberFacadeException($exception->getMessage());
        }
    }



    /**
     * Create subscriber object.
     * @param $email string
     * @return Subscriber
     * @throws \EntityInvalidArgumentException
     */
    private function createSubscriber(string $email) : Subscriber
    {
        $subscriber = new Subscriber();
        $subscriber->setConfirmed(FALSE);
        $subscriber->setEmail($email, $this->translator);
        $subscriber->setConfirmToken(Subscriber::generateConfirmToken());
        return $subscriber;
    }
}