<?php

declare(strict_types = 1);

namespace App\Product\Command;

use App\BaseCommand;
use App\Product\ProductRepository;
use App\Product\Translation\ProductTranslation;
use App\Product\Translation\ProductTranslationSaveFacade;
use App\Product\Translation\ProductTranslationSaveFacadeException;
use App\Product\Translation\ProductTranslationSaveFacadeFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class GenerateUrlCommand extends BaseCommand
{


    /** @var string */
    const COMMAND = 'product:url:generate';

    const FORCE = 'force';

    /** @var ProductTranslationSaveFacade */
    private $productTranslationFacade;

    /** @var ProductRepository */
    private $productRepo;



    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->productTranslationFacade = $this->container->getByType(ProductTranslationSaveFacadeFactory::class)->create();
        $this->productRepo = $this->container->getByType(ProductRepository::class);
    }



    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Generate and rewrite url for products')
            ->addOption(self::FORCE, 'f', InputOption::VALUE_NONE, 'Fuse for rewrite.');
    }



    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption(self::FORCE)) {
            $output->writeln('You must use --force option.');
            return 1;
        }

        $updated = 0;
        $error = 0;

        Debugger::timer();
        $bulk = 200;
        $productCount = $this->productRepo->count([]);

        //info message
        $message = 'Start generating and rewriting url for products.';
        $this->logger->addDebug($message);
        $output->writeln($message);

        for ($i = 0; $i <= $productCount->getCount();) {
            $products = $this->productRepo->findByLimitAndOffset($bulk, $i);
            foreach ($products as $product) {
                $productTranslations = $product->getTranslations();
                /** @var ProductTranslation $productTranslation */
                foreach ($productTranslations as $productTranslation) {

                    try {
                        $this->database->beginTransaction();
                        $translation = $this->productTranslationFacade->update($productTranslation->getId(), $productTranslation->getName(), $productTranslation->getDescription(), null, $productTranslation->getTitleSeo(),
                            $productTranslation->getDescriptionSeo(), $productTranslation->getShortDescription(), $productTranslation->getTitleOg(), $productTranslation->getDescriptionOg());
                        $this->database->commit();

                        //message
                        $message = sprintf('Url for product \'%d\' with name \'%s\' for translation id \'%d\' has been generated. Url: %s.',
                            $productTranslation->getProductId(), $productTranslation->getName(), $productTranslation->getId(), $translation->getUrl());
                        $this->logger->addInfo($message);
                        $output->writeln($message);

                        $updated++;
                        unset($translation);
                    } catch (ProductTranslationSaveFacadeException $exception) {
                        $this->database->rollBack();

                        $message = sprintf('An error has been occurred on generating url for product with id \'%d\' with translation id \'%d\'. Error: %s', $product->getId(), $productTranslation->getId(), $exception->getMessage());
                        $this->logger->addError($message);
                        $output->writeln($message);
                        $error++;
                    }
                }
            }
            $i += $bulk;
        }

        //message
        $message = sprintf('Finished generating and rewriting url for products in time \'%s\' seconds. Updated: %d. Error: %d', Debugger::timer(), $updated, $error);
        $this->logger->addInfo($message);
        $output->writeln($message);

        return 0;
    }
}