<?php

declare(strict_types = 1);

namespace App\PeriskopModule\Component\Export;

use App\Customer\Customer;
use App\Customer\CustomerRepository;
use App\Helpers\Entities;
use App\Opportunity\OpportunityRepository;
use App\Order\Order;
use App\Order\OrderRepository;
use App\Periskop\Export\ExportFacadeFactory;
use App\Periskop\Export\ExportRepository;
use Kdyby\Monolog\Logger;
use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Database\Context;
use Tracy\Debugger;
use Tracy\ILogger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Export extends Control
{


    /** @var string */
    const LOGGER_NAMESPACE = 'periskop.export';
    protected $database;

    /** @var CustomerRepository */
    private $customerRepo;

    /** @var ExportFacadeFactory */
    private $exportFacadeFactory;

    /** @var ExportRepository */
    private $exportRepo;

    /** @var ILatteFactory */
    private $latteFactory;

    /** @var Logger */
    private $logger;

    /** @var OpportunityRepository */
    private $opportunityRepo;

    /** @var OrderRepository */
    private $orderRepo;

    /** @var string|null */
    private $type;



    public function __construct(CustomerRepository $customerRepo,
                                ExportFacadeFactory $exportFacadeFactory,
                                ExportRepository $exportRepository,
                                ILatteFactory $latteFactory,
                                Logger $logger,
                                OpportunityRepository $opportunityRepository,
                                OrderRepository $orderRepository,
                                Context $database)
    {
        $this->database = $database;
        parent::__construct();
        $this->customerRepo = $customerRepo;
        $this->exportFacadeFactory = $exportFacadeFactory;
        $this->exportRepo = $exportRepository;
        $this->latteFactory = $latteFactory;
        $this->logger = $logger;
        $this->opportunityRepo = $opportunityRepository;
        $this->orderRepo = $orderRepository;
    }


    /**
     * @param $type string
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }


    public function render()
    {
        Debugger::timer();

        //create file content
        $content = $this->getContent();

        //save file
        $fileName = \App\Periskop\Export\Export::generateFileName($this->type);
        $absoluteFileName = \App\Periskop\Export\Export::getAbsoluteFilePath($this->getPresenter()->context, $fileName);
        $this->saveFile($content, $absoluteFileName);
        //saveBackup
        $backupFileName = \App\Periskop\Export\Export::getAbsoluteFilePath($this->getPresenter()->context, "backup/" . $fileName);
        $this->saveFile($content, $backupFileName);

        //save export
        $exportFacade = $this->exportFacadeFactory->create();
        try {
            $export = $exportFacade->add($fileName, $this->type);
        } catch (\Exception $e) {
            Debugger::log($e, ILogger::EXCEPTION);
            throw $e;
        }

        $this->logger->addInfo(sprintf(self::LOGGER_NAMESPACE . ': Generování exportního souboru s názvem \'%s\' pro typ \'%s\' dokončeno v čase %s sekund. ID: %d', $fileName, $this->type, Debugger::timer(), $export->getId()));

        //render response
        $this->template->export = $export;
        $this->template->setFile(__DIR__ . '/templates/response.latte');
        $this->template->render();
    }



    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getContent() : string
    {
        $content = NULL;
        $latte = $this->latteFactory->create();
        if ($this->type === \App\Periskop\Export\Export::TYPE_CUSTOMER) {
            $customers = $this->getCustomers();
            $content = $latte->renderToString(__DIR__ . '/templates/customer.latte', ['customers' => $customers, 'control' => $this]);
            $this->logger->addDebug(sprintf(self::LOGGER_NAMESPACE . ': Nalezeno celkem \'%s\' zákazníků pro export.', count($customers)), ['id' => Entities::getProperty($customers, 'id')]);
        } elseif ($this->type === \App\Periskop\Export\Export::TYPE_ORDER) {
            $orders = $this->getOrders();
            $content = $latte->renderToString(__DIR__ . '/templates/order.latte', ['orders' => $orders, 'control' => $this]);
            $this->logger->addDebug(sprintf(self::LOGGER_NAMESPACE . ': Nalezeno celkem \'%s\' objednávek pro export.', count($orders)), ['id' => Entities::getProperty($orders, 'id')]);
        } else {
            throw new \InvalidArgumentException(sprintf('Missing content builder for type \'%s\'.', $this->type));
        }

        return $content;
    }


    /*
     * workaround to periskop bug - it's using customer address at invoices instead of invoice address
     * so to (at least partialy) circumvent, change customer address when there is invoice with different address
      */
    protected function updateCustomersAddresses()
    {
        $orders = $this->orderRepo->findForExportToExternalSystem();
        /** @var Order $order */
        foreach ($orders as $order) {
            if ($order->getCustomerId()) {
                /** @var Customer $customer */
                $customer = $this->customerRepo->getOneById($order->getCustomerId());
                if ($order->getBillingAddressCity() != $customer->getCity()
                    || $order->getBillingAddressCountry() != $customer->getCountryCode()
                    || $order->getBillingAddressPostcode() != $customer->getPostcode()
                    || $order->getBillingAddressStreet() != $customer->getStreet()
                    || $order->getCustomerFirstName() != $customer->getFirstName()
                    || $order->getCustomerLastName() != $customer->getLastName()
                ) {
                    $customer->setFirstName($order->getCustomerFirstName());
                    $customer->setLastName($order->getCustomerLastName());
                    $customer->setCity($order->getBillingAddressCity());
                    $customer->setCountryCode($order->getBillingAddressCountry());
                    $customer->setPostcode($order->getBillingAddressPostcode());
                    $customer->setStreet($order->getBillingAddressStreet());
                    $this->customerRepo->save($customer);

                    bdump($order);
                    bdump($customer);
                }
            }
        }
    }

    /**
     * @return Customer[]|array
     */
    private function getCustomers(): array
    {
        $this->updateCustomersAddresses();
        $newCustomers = $this->customerRepo->findWithoutExternalSystemId();
        $updatedCustomers = $this->customerRepo->findUpdatedFromDate($this->getLastExportDate(\App\Periskop\Export\Export::TYPE_CUSTOMER));
        return array_merge($newCustomers, $updatedCustomers);
    }


    /**
     * @return Order[]|array
     */
    private function getOrders(): array
    {
        return $this->orderRepo->findForExportToExternalSystem();
    }


    /**
     * @param $content string
     * @param $fileName string
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function saveFile(string $content, string $fileName) : bool
    {
        $file = fopen($fileName, "w");
        if ($file === FALSE) {
            $lastError = error_get_last();
            throw new \InvalidArgumentException(sprintf('File \'%s\' has not been created with error: %s.', $fileName, print_r($lastError, TRUE)));
        }
        fwrite($file, $content);
        fclose($file);

        return true;
    }


    /**
     * @param $type string
     * @return \DateTime
     */
    private function getLastExportDate(string $type) : \DateTime
    {
        $lastExport = $this->exportRepo->findOneLastByType($type);
        return new \DateTime($lastExport ? $lastExport->getAddDate() : '2000-01-01 00:00:00');
    }



    /**
     * Convert text to Periskop encoding
     * @param $text mixed
     * @return mixed
    */
    public function encoding($text = NULL)
    {
        return $text ? iconv('UTF-8', 'Windows-1250//TRANSLIT', $text) : NULL;
    }
}
