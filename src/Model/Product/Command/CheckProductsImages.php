<?php

declare(strict_types=1);

namespace App\Product\Command;

use App\BaseCommand;
use App\FrontModule\Components\Product\GoogleMerchantFeed\GoogleMerchantFeed;
use App\FrontModule\Components\Product\GoogleMerchantFeed\GoogleMerchantFeedFactory;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CheckProductsImages extends BaseCommand
{


    /** @var string */
    const COMMAND = 'product:check:images';


    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Check products for missing images.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Debugger::timer();
        $missingCount = 0;
        foreach ($this->database->table('product')->where('p_state="publish" AND p_sale_online = 1') as $p) {
            if ($p->p_photo) {
                $path = '/var/www/jk.cz/web/www/upload/products/' . $p->p_id . '/' . $p->p_photo;
                if (!file_exists($path)) {
                    $message = sprintf('Product %s missing image %s', $p->p_code, $p->p_id . '/' . $p->p_photo);
                    $this->logger->addInfo($message);
                    $output->writeln($message);
                    $missingCount++;
                }
            }
        }

        //summary message
        $message = sprintf('%d missing images, time: %f seconds.', $missingCount, Debugger::timer());
        $this->logger->addInfo($message);
        $output->writeln($message);

        return 0;
    }
}
