<?php

declare(strict_types = 1);

namespace App\Product\Command;

use App\BaseCommand;
use App\FrontModule\Components\Product\FacebookOptimizedFeed\FacebookOptimizedFeed;
use App\FrontModule\Components\Product\FacebookOptimizedFeed\FacebookOptimizedFeedFactory;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class FacebookFeedCommand extends BaseCommand
{

    /** @var string */
    const COMMAND = 'product:facebook:generate';

    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Generate feed for Facebook.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Debugger::timer();
        $storage = $this->container->getByType(IStorage::class);

        //clean cache
        $cache = new Cache($storage, 'Nette.Templating.Cache');
        $cache->clean([
            Cache::TAGS => [FacebookOptimizedFeed::CACHE_TAG]
        ]);

        //generate
        $presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');
        $presenter = $presenterFactory->createPresenter('Front:Product');
        $controlFactory = $this->container->getByType(FacebookOptimizedFeedFactory::class);
        $control = $controlFactory->create();
        $control->setParent($presenter);
        $control->renderToString();

        //summary message
        $message = sprintf('Facebook feed has been generated. Time: %f seconds.', Debugger::timer());
        $this->logger->addInfo($message);
        $output->writeln($message);

        return 0;
    }
}