<?php

declare(strict_types = 1);

namespace App\Customer\Activation;

use App\BaseCommand;
use App\Customer\Customer;
use App\Customer\CustomerRepository;
use App\Customer\CustomerRepositoryFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SendRequestCommand extends BaseCommand
{


    /** @var string */
    const SEND_OPTION = 'send';

    /** @var ActivationFacade */
    private $customerActivationFacade;

    /** @var CustomerRepository */
    private $customerRepo;



    protected function configure()
    {
        parent::configure();
        $this->setName('customer:activation:request')
            ->setDescription('Send activation request to customers who are not activated yet.')
            ->addOption(self::SEND_OPTION, 's', InputOption::VALUE_NONE, 'Confirm send requests.');
    }



    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $customerActivationFacadeFactory = $this->container->getByType(ActivationFacadeFactory::class);
        $customerRepoFactory = $this->container->getByType(CustomerRepositoryFactory::class);
        $this->customerActivationFacade = $customerActivationFacadeFactory->create();
        $this->customerRepo = $customerRepoFactory->create();
    }



    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sent = 0;
        $error = 0;

        $emails = $this->customerRepo->findEmailsForActivation();

        //if is not confirmed sending, show only information about count of emails
        if ($input->getOption(self::SEND_OPTION) === FALSE) {
            $output->writeln(sprintf('Celkem čeká na odeslání žádosti o aktivaci %d e-mailů. Pro odeslání použijte parametr --%s.', count($emails), self::SEND_OPTION));
            return 0;
        }

        foreach ($emails as $email) {
            try {
                $this->database->beginTransaction();
                $customer = $this->customerActivationFacade->createRequest($email);
                $this->database->commit();

                $this->requestLog($customer, $output);
                unset($customer);
                $sent++;
            } catch (ActivationFacadeException $exception) {
                $this->database->rollBack();
                $this->writeWarningMessage($output, $exception->getMessage(), [
                	'email' => $email,
				]);
                $error++;
            }
        }

        $this->processFinishedLog($output, $sent, $error);
        return 0;
    }



    /**
     * Log of created request.
     * @param $customer Customer
     * @param $output OutputInterface
     * @return Customer
     */
    private function requestLog(Customer $customer, OutputInterface $output) : Customer
    {
        $message = sprintf('Byla odeslána žádost o aktivaci účtu na e-mail %s pro zákazníka s externím id %d.', $customer->getEmail(), $customer->getExternalSystemId());
        $this->logger->addInfo($message);
        $output->writeln($message);
        return $customer;
    }



    /**
     * Log of finished process.
     * @param $output OutputInterface
     * @param $sent int
     * @param $error int
     * @return OutputInterface
     */
    private function processFinishedLog(OutputInterface $output, int $sent, int $error) : OutputInterface
    {
        $message = sprintf('Bylo odesláno celkem %d žádostí o aktivaci účtu. Neodesláno s chybou %d.', $sent, $error);
        $this->logger->addInfo($message);
        $output->writeln($message);
        return $output;
    }
}