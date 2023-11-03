<?php

declare(strict_types = 1);

namespace App\Product\Command;

use App\BaseCommand;
use App\FrontModule\Components\Product\GoogleMerchantFeed\GoogleMerchantFeed;
use App\FrontModule\Components\Product\GoogleMerchantFeed\GoogleMerchantFeedFactory;
use App\FrontModule\Components\Product\GoogleMerchantOptimizedFeed\GoogleMerchantOptimizedFeedFactory;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class GoogleMerchantCommand extends BaseCommand
{

    /** @var string */
    const COMMAND = 'product:googlemerchant:generate';

    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Generate feed for Google Merchant.');
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Debugger::timer();
        $storage = $this->container->getByType(IStorage::class);

        //clean cache
        $cache = new Cache($storage, 'Nette.Templating.Cache');
        $cache->clean([
            Cache::TAGS => [GoogleMerchantFeed::CACHE_TAG]
        ]);

        //generate
        $presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');
        $presenter = $presenterFactory->createPresenter('Front:Product');
        $controlFactory = $this->container->getByType(GoogleMerchantOptimizedFeedFactory::class);
        $control = $controlFactory->create();
        $control->setParent($presenter);
        $control->renderToString();

        //summary message
        $message = sprintf('Google merchant feed has been generated. Time: %f seconds.', Debugger::timer());
        $this->logger->addInfo($message);
        $output->writeln($message);

        return 0;
    }
}