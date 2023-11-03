<?php

declare(strict_types = 1);

namespace App\Product\Command;

use App\BaseCommand;
use App\FrontModule\Components\Product\HeurekaOptimizedFeed\HeurekaOptimizedFeed;
use App\FrontModule\Components\Product\HeurekaOptimizedFeed\HeurekaOptimizedFeedFactory;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class HeurekaFeedGenerateCommand extends BaseCommand
{


    /** @var string */
    const COMMAND = 'product:heureka:generate';



    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Generate product feed for Heureka.');
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Debugger::timer();
        $storage = $this->container->getByType(IStorage::class);

        //clean cache
        $cache = new Cache($storage, 'Nette.Templating.Cache');
        $cache->clean([
            Cache::TAGS => [HeurekaOptimizedFeed::CACHE_TAG]
        ]);

        //generate
        $presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');
        $presenter = $presenterFactory->createPresenter('Front:Feed');
        $controlFactory = $this->container->getByType(HeurekaOptimizedFeedFactory::class);
        $control = $controlFactory->create();
        $control->setParent($presenter);
        $xmlContents = $control->renderToString();

        //summary message
        $message = sprintf('Heureka product feed has been generated. Time: %f seconds.', Debugger::timer());
        $this->logger->addInfo($message);
        $output->writeln($message);

        // copy the XML file to a public directory
        file_put_contents(
            __DIR__ . '/../../../../www/feed/heureka.xml',
            $xmlContents
        );

        return 0;
    }
}