<?php

declare(strict_types = 1);

namespace App\Periskop\Product\Import;

use App\Periskop\Xml\AbstractXmlCommand;
use App\Product\ProductRepository;
use App\Product\ProductSaveFacade;
use App\Product\ProductSaveFacadeException;
use App\Product\ProductSaveFacadeFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductStockImportCommand extends AbstractXmlCommand
{


    /** @var string */
    const COMMAND = 'periskop:product:stock:import';

    /** @var ProductRepository|null */
    private $productRepo;

    /** @var ProductSaveFacade|null */
    private $productSaveFacade;



    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->productRepo = $this->container->getByType(ProductRepository::class);
        $this->productSaveFacade = $this->container->getByType(ProductSaveFacadeFactory::class)->create();
    }



    protected function configure()
    {
        parent::configure();
        $this->setName('periskop:product:stock:import')
            ->setDescription('Run import stock of products from a file.');
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
            $items = $xmlFile->xpath('/stock/item');
            $externalSystemIdList = $items ? $this->productRepo->findExternalSystemIdList() : [];
        } catch (\InvalidArgumentException $exception) {
        	$this->writeErrorMessage($output, $exception->getMessage(), [
        		'filePath' => $filePath,
			]);
            return 1;
        }

        foreach ($items as $item) {
            if ($this->checkRequiredElements($item, $output, ['item_id', 'amount']) === TRUE) {
                $externalSystemId = (string)$item->item_id;
                $amount = (int)$item->amount;
                $productId = $externalSystemIdList[$externalSystemId] ?? NULL;
                if ($productId === NULL) {
                	$this->writeNoticeMessage($output, sprintf('Chybí produkt v eshopu s externím id \'%d\' pro uložení skladového množství \'%d\'.', $externalSystemId, $amount));
                    continue;
                }

                try {
                    $this->database->beginTransaction();
                    $product = $this->productSaveFacade->saveStockQuantity($productId, $amount);
                    $this->database->commit();
                    unset($externalSystemIdList[$externalSystemId], $product);
                    $imported++;

                    $this->writeInfoMessage($output, sprintf('Bylo uloženo skladové množství \'%d\' pro produkt s externím id \'%d\'.', $amount, $externalSystemId));
                } catch (ProductSaveFacadeException $exception) {
                    $this->database->rollBack();
                    $this->writeErrorMessage($output, sprintf('Nastala chyba při ukládání skladového množství pro produkt s externím id \'%d\'. Chyba: %s', $externalSystemId, $exception->getMessage()));
                    $error++;
                }
            }
        }

        $missingCount = count($externalSystemIdList);
        if ($missingCount > 0) {
        	$this->writeWarningMessage($output, sprintf('Nebylo nalezeno skladové množství pro produkty s externím id: \'%s\'', implode('\',\'', array_keys($externalSystemIdList))));
        }

        $this->writeInfoMessage($output, sprintf('Import skladového množství produktů ze souboru \'%s\' byl dokončen v čase %s sekund. Importováno: %d. Neimportováno s chybou: %d. Chybějících produktů v importním souboru: %d.', $filePath, Debugger::timer(), $imported, $error, $missingCount));
        return 0;
    }
}