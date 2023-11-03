<?php

declare(strict_types=1);

namespace App\Customer\Command;

use App\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;
use App\Customer\CustomerRepository;
use App\Order\OrderRepository;


final class SetOnceBirthdayCoupons extends BaseCommand
{


    /** @var string */
    const COMMAND = 'customer:oncebirthdaycoupon:set';

		
    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Set one time birthday coupon for customers according to the current date');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Debugger::timer();
        $repoCustomer = $this->container->getByType(CustomerRepository::class);
        $repoOrder = $this->container->getByType(OrderRepository::class);

				
				//prednacteme si casy poslednich objednavek zakazniku
				$lastOrders = array();
        foreach ($repoOrder->findAll() as $order) {
						if (isset($lastOrders[$order->getCustomerId()])) {
								$lastOrders[$order->getCustomerId()] = $order->getAddDate();
						}
						else {
								if ($lastOrders[$order->getCustomerId()] < $order->getAddDate()) {
										$lastOrders[$order->getCustomerId()] = $order->getAddDate();
								}
						}
				}
				
        $couponActivationCount = 0;
				$lastYearToday = date("Y-m-d", strtotime("-1 year", time()));
				
				//projdeme vsechny zakazniky
        foreach ($repoCustomer->findAll() as $customer) {
						//zakaznik musi mit nastaveny mesic a rok narozeni
						if (!$customer->getBirthdayMonth() || !$customer->getBirthdayYear()) {
								continue;
						}

						$hasCoupon = false;
						//pokud je registrace starsi nez rok, ma narok na kupon
						if ($customer->getAddDate() <= $lastYearToday) {
								$hasCoupon = true;
						}
						//registrace novejsi nez rok
						else {
								//pokud ma narozeniny (jen mesic) kalendarne driv, nez je jeho datum registrace, ma narok 
								$birthdayDate = date("Y")."-".$customer->getBirthdayMonth();
								if ($birthdayDate <= $customer->getAddDate()) {
										$hasCoupon = true;
								}
						}

						
						//pokud ma narok na kupon dle data registrace
						if ($hasCoupon) {
								//kdo od poslednich narozenin udelal objednavku, nema narok
								if ($customer->getBirthdayMonth() > date("m")) {
										$lastYear = date("Y") - 1;
										$lastBirthday = $lastYear."-".$customer->getBirthdayMonth();		
								}
								else {
										$lastBirthday = date("Y")."-".$customer->getBirthdayMonth();											
								}
								if (isset($lastOrders[$customer->getId()]) && $lastOrders[$customer->getId()] > $lastBirthday) {
										continue;
								}

								//ok, ma narok na kupon, ulozime
								$customer->setBirthdayCoupon(true);
								$repoCustomer->save($customer);
								$couponActivationCount++;							
						}
						
        }

        //summary message
        $message = sprintf('%d coupons activated, time: %f seconds.', $couponActivationCount, Debugger::timer());
        $this->logger->addInfo($message);
        $output->writeln($message);

        return 0;
    }
}
