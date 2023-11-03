<?php

declare(strict_types = 1);

namespace App\Product\Command;

use App\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductFeedGenerateCommand extends BaseCommand
{


    protected function configure()
    {
        parent::configure();
        $this->setName('product:productfeed:generate')
            ->setDescription('Generate product feeds.');
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //generate data to products for feeds
        $this->getApplication()->find(ProductFeedGenerateDataCommand::COMMAND)->run($input, $output);

        //google merchant feed
        $this->getApplication()->find(GoogleMerchantCommand::COMMAND)->run($input, $output);

        //facebook feed
        $this->getApplication()->find(FacebookFeedCommand::COMMAND)->run($input, $output);

        //heureka
        $this->getApplication()->find(HeurekaFeedGenerateCommand::COMMAND)->run($input, $output);

        //zbozi cz
        $this->getApplication()->find(ZboziCzFeedGenerateCommand::COMMAND)->run($input, $output);

        return 0;
    }
}