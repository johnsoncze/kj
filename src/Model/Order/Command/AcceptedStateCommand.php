<?php

declare(strict_types = 1);

namespace App\Order\Command;

use App\BaseCommand;
use App\Helpers\Entities;
use App\Order\Order;
use App\Order\OrderRepository;
use App\Order\OrderStateFacade;
use App\Order\OrderStateFacadeException;
use App\Order\OrderStateFacadeFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class AcceptedStateCommand extends BaseCommand
{


    /** @var string */
    const LOGGER_NAMESPACE = 'order.state.set.accepted';



    protected function configure()
    {
        parent::configure();
        $this->setName('order:state:set:accepted')
            ->setDescription('Set accepted state for new orders.');
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Debugger::timer();

        $set = 0;
        $error = 0;
        $state = Order::ACCEPTED_STATE;

        /**
         * @var $orderStateFacade OrderStateFacade
         * @var $orderRepo OrderRepository
        */
        $orderStateFacade = $this->container->getByType(OrderStateFacadeFactory::class)->create();
        $orderRepo = $this->container->getByType(OrderRepository::class);
        $orders = $orderRepo->findWaitingForAcceptedStateByCreatedDate(new \DateTime('-1 hours'));
        $orderId = $orders ? Entities::getProperty($orders, 'id') : [];

        $this->writeInfoMessage($output, sprintf(self::LOGGER_NAMESPACE . ': Start process for set \'%s\' state for new orders. Found orders: %d', $state, count($orders)), [
			'orderId' => $orderId,
		]);

        foreach ($orders as $order) {
            try {
                $this->database->beginTransaction();
                $_order = $orderStateFacade->set($order->getId(), Order::ACCEPTED_STATE);
                $this->database->commit();

                $this->writeInfoMessage($output, sprintf(sprintf('State \'%s\' for order \'%s\' (ID: %d) has been set.', $state, $order->getCode(), $order->getId())));

                unset($order, $_order);
                $set++;
            } catch (OrderStateFacadeException $exception) {
                $this->database->rollBack();
                $this->writeErrorMessage($output, sprintf('An error has been occurred on set \'%s\' state for order \'%s\' (ID: %d). Error: %s', $state, $order->getCode(), $order->getId(), $exception->getMessage()));
                $error++;
            }
        }

        $this->writeInfoMessage($output, sprintf('Process for set \'%s\' state for new orders has been finished in \'%s\' seconds. Passed: %d. Error: %d.', $state, Debugger::timer(), $set, $error));

        return 0;
    }
}