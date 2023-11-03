<?php

declare(strict_types=1);

namespace App\Customer\Command;

use App\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;
use App\Customer\CustomerRepository;


final class SetBirthdayCoupons extends BaseCommand
{


    /** @var string */
    const COMMAND = 'customer:birthdaycoupon:set';

		
    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Set birthday coupon for customers');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->container->getByType(CustomerRepository::class);
				
        Debugger::timer();
        $couponActivationCount = 0;
        foreach ($repo->findAll() as $customer) {
						$hasCoupon = $customer->shouldHaveBirthDayCoupon();
						if ($hasCoupon && !$customer->getBirthdayCoupon()) {
								$customer->setBirthdayCoupon(true);
								$repo->save($customer);
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
