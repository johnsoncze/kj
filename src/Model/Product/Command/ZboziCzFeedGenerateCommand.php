<?php

declare(strict_types = 1);

namespace App\Product\Command;

use App\BaseCommand;
use App\FrontModule\Components\Product\ZboziCzFeed\ZboziCzFeed;
use App\FrontModule\Components\Product\ZboziCzFeed\ZboziCzFeedFactory;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ZboziCzFeedGenerateCommand extends BaseCommand
{


    /** @var string */
    const COMMAND = 'product:zbozicz:generate';



    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Generate feed for Zbozi.cz.');
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Debugger::timer();
        $storage = $this->container->getByType(IStorage::class);

        //clean cache
        $cache = new Cache($storage, 'Nette.Templating.Cache');
        $cache->clean([
            Cache::TAGS => [ZboziCzFeed::CACHE_TAG]
        ]);

        //generate
        $presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');
        $presenter = $presenterFactory->createPresenter('Front:Feed');
        $controlFactory = $this->container->getByType(ZboziCzFeedFactory::class);
        $control = $controlFactory->create();
        $control->setParent($presenter);
        $control->renderToString();

        //summary message
        $message = sprintf('Zbozi.cz feed has been generated. Time: %f seconds.', Debugger::timer());
        $this->logger->addInfo($message);
        $output->writeln($message);

        return 0;
    }
}