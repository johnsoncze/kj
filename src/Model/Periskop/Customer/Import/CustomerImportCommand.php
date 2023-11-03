<?php

declare(strict_types = 1);

namespace App\Periskop\Customer\Import;

use App\Customer\Customer;
use App\Customer\CustomerRepository;
use App\Customer\CustomerRepositoryFactory;
use App\Customer\CustomerStorageException;
use App\Customer\CustomerStorageFacade;
use App\Customer\CustomerStorageFacadeFactory;
use App\Helpers\Entities;
use App\Periskop\Xml\AbstractXmlCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CustomerImportCommand extends AbstractXmlCommand
{

    /** @var string base command */
    const COMMAND = 'periskop:customer:import';

    /** @var CustomerRepository */
    private $customerRepo;

    /** @var CustomerStorageFacade */
    private $customerStorageFacade;



    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Run import customers from xml file.');
    }



    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $customerRepositoryFactory = $this->container->getByType(CustomerRepositoryFactory::class);
        $customerStorageFacadeFactory = $this->container->getByType(CustomerStorageFacadeFactory::class);
        $this->customerRepo = $customerRepositoryFactory->create();
        $this->customerStorageFacade = $customerStorageFacadeFactory->create();
    }



    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $new = 0;
        $updated = 0;
        $error = 0;
        $filePath = $input->getArgument(self::FILE_ARGUMENT);

        try {
            $xmlFile = $this->getFile($filePath);
        } catch (\InvalidArgumentException $exception) {
        	$this->writeErrorMessage($output, $exception->getMessage(), [
        		'filePath' => $filePath,
			]);
            return 1;
        }

        Debugger::timer();
        $customers = $xmlFile->xpath('/data/customers/customer'); //get only customers from xml
        $customerList = $this->customerRepo->findExternalSystemIdList();
        $customersWithoutExternalId = $this->customerRepo->findWithoutExternalSystemId();
        $customersWithoutExternalId = $customersWithoutExternalId ? Entities::setValueAsKey($customersWithoutExternalId, 'email') : [];
        foreach ($customers as $customer) {

            $externalSystemId = (int)$customer['id'];
            $lastChange = (string)$customer['last_change'];

            try {
                //check required elements
                $requiredElements = ['email', 'firstname', 'lastname'];
                if ($this->checkRequiredElements($customer, $output, $requiredElements) === TRUE) {

                    $email = (string)$customer->email;
                    $firstName = (string)$customer->firstname;
                    $lastName = (string)$customer->lastname;
                    $telephone = str_replace(' ', '', (string)$customer->phone) ?: NULL;
                    $addressing = (string)$customer->addressing ?: NULL;
                    $street = (string)$customer->street ?: NULL;
                    $city = (string)$customer->city ?: NULL;
                    $postcode = (int)str_replace(' ', '', (string)$customer->postcode) ?: NULL;
                    $country = (string)$customer->country ?: NULL;
                    $birthdayYear = (int)$customer->birthdayyear ?: NULL;
                    $birthdayMonth = (int)$customer->birthdaymonth ?: NULL;
                    $birthdayDay = (int)$customer->birthdayday ?: NULL;
                    $birthdayCoupon = (string)$customer->birthdaycoupon === 'A';
                    $newsletter = (string)$customer->newsletter === 'A';
                    $sex = strtolower((string)$customer->sex) ?: NULL;
                    $id = $customerList[$externalSystemId]['id'] ?? NULL;
                    $code = (string)$customer->code ?: NULL;
                    $customerWithoutExternalId = $customersWithoutExternalId[$email] ?? NULL;
                    !$id && $customerWithoutExternalId && $id = $customerWithoutExternalId->getId();

                    if ($customerWithoutExternalId
                        || !isset($customerList[$externalSystemId])
                        || (isset($customerList[$externalSystemId]) && $lastChange >= $customerList[$externalSystemId]['externalSystemLastChangeDate'])) {
                        $this->database->beginTransaction();

                        $customerObject = $this->customerStorageFacade->save($id, $email, $firstName, $lastName, $sex, $externalSystemId, NULL, $telephone, $addressing, $street, $city,
                            $postcode, $country, $birthdayYear, $birthdayMonth, $birthdayDay, $birthdayCoupon, $newsletter, $lastChange, Customer::ALLOWED, $code);

                        $this->database->commit();
                        $this->logSavedCustomer($customerObject, $output);
                        $id !== NULL ? $updated++ : $new++;
                        unset($customerObject);
                    }
                }
            } catch (CustomerStorageException $exception) {
                $this->database->rollBack();
                $this->writeErrorMessage($output, $exception->getMessage(), [
                	'externalSystemId' => $externalSystemId,
				]);
                $error++;
            }
        }

        $message = sprintf('Import zákazníků ze souboru \'%s\' byl dokončen v čase %s. Bylo importováno nových %d a aktualizováno %d zákazníků. Neuloženo s chybou %d.', $filePath, Debugger::timer(), $new, $updated, $error);
        $this->writeInfoMessage($output, $message);
        return 0;
    }



    /**
     * Log saved customer.
     * @param $customer Customer
     * @param $output OutputInterface
     * @return Customer
     */
    private function logSavedCustomer(Customer $customer, OutputInterface $output) : Customer
    {
        $message = sprintf('Byl uložen zákazník s externím id %d.', $customer->getExternalSystemId());
        $this->writeInfoMessage($output, $message);

        return $customer;
    }

}