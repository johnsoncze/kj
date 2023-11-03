<?php

declare(strict_types = 1);

namespace App\Product\Command;

use App\BaseCommand;
use App\FrontModule\Components\Product\SiteMap\SiteMapFactory;
use App\Language\LanguageRepository;
use Nette\Application\IPresenterFactory;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SiteMapCommand extends BaseCommand
{


    protected function configure()
    {
        parent::configure();
        $this->setName('product:sitemap:generate')
            ->setDescription('Generate sitemaps with products.');
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sitemapCount = 0;
        Debugger::timer();
        $presenterFactory = $this->container->getByType(IPresenterFactory::class);
        $presenter = $presenterFactory->createPresenter('Front:Sitemap');

        /** @var $controlFactory SiteMapFactory */
        $controlFactory = $this->container->getByType(SiteMapFactory::class);
        $control = $controlFactory->create();
        $control->setParent($presenter);

        //clear cache
        $storage = $this->container->getByType(IStorage::class);
        $cache = new Cache($storage, 'Nette.Templating.Cache');

        //generate sitemap for all languages
        $languageRepo = $this->container->getByType(LanguageRepository::class);
        $languages = $languageRepo->findActive();
        foreach ($languages as $language) {
            $control->setLanguage($language);
            $cache->clean([Cache::TAGS => [$control->getCacheId()]]);
            $control->renderToString();
            $sitemapCount++;
            $this->logger->addInfo(sprintf('Product sitemap for language \'%s\' has been generated.', $language->getPrefix()));
        }

        $message = sprintf('Product sitemaps have been generated in %f seconds. Summary %d sitemaps.', Debugger::timer(), $sitemapCount);
        $output->writeln($message);
        $this->logger->addInfo($message);
        return 0;
    }
}