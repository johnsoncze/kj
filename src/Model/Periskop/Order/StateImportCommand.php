<?php

declare(strict_types = 1);

namespace App\Periskop\Order;

use App\Order\OrderStateFacade;
use App\Order\OrderStateFacadeException;
use App\Order\OrderStateFacadeFactory;
use App\Periskop\Xml\AbstractXmlCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class StateImportCommand extends AbstractXmlCommand
{


    /** @var string */
    const COMMAND = 'periskop:order:state:import';

    /** @var OrderStateFacade|null */
    private $orderStateFacade;



    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->orderStateFacade = $this->container->getByType(OrderStateFacadeFactory::class)->create();
    }



    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Run import states of orders from a file.');
    }



    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $imported = 0;
        $error = 0;

        Debugger::timer();
        $filePath = $input->getArgument(self::FILE_ARGUMENT);

        try {
            $xmlFile = $this->getFile($filePath);
            $items = $xmlFile->xpath('/orders/order');
        } catch (\InvalidArgumentException $exception) {
        	$this->writeErrorMessage($output, $exception->getMessage(), [
        		'filePath' => $filePath,
			]);
            return 1;
        }

        foreach ($items as $item) {
            if ($this->checkRequiredElements($item, $output, ['state']) === TRUE) {
                $orderId = OrderHelpers::getOrderId((int)$item['id']);
                $state = (int)$item->state;

                try {
                    $this->database->beginTransaction();
                    $order = $this->orderStateFacade->setByExternalSystemStateId($orderId, $state);
                    $this->database->commit();

                    $this->writeInfoMessage($output, sprintf('Byl importován stav s externím id \'%d\' (%s) pro objednávku \'%s\'.', $state, $order->getState(), $order->getCode()));
                    unset($order);
                    $imported++;

                } catch (OrderStateFacadeException $exception) {
                    $this->database->rollBack();
                    $this->writeErrorMessage($output, sprintf('Nastala chyba při importu stavu pro objednávku s id \'%d\'. Chyba: %s', $orderId, $exception->getMessage()));
                    $error++;
                }
            }
        }

        //summary message
		$this->writeInfoMessage($output, sprintf('Import se stavem objednávek ze souboru \'%s\' byl dokončen v čase %s sekund. Importováno: %d. Neimportováno s chybou: %d.', $filePath, Debugger::timer(), $imported, $error));
        return 0;
    }
}