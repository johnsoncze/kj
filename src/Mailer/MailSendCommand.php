<?php

declare(strict_types=1);

namespace App\Email;

use App\BaseCommand;
use App\Facades\MailerFacade;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class MailSendCommand extends BaseCommand
{

    /** @var MailerFacade */
    protected $mailerFacade;

    /** @var string */
    const COMMAND = 'emails:send';

    /**
     * @param $name
     * @param MailerFacade|null $mailerFacade
     */
    public function __construct($name = null, MailerFacade $mailerFacade = null)
    {
        parent::__construct($name);
        $this->mailerFacade = $mailerFacade;
    }


    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Send emails from queue.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->mailerFacade->sendQueueEmails();

        //$message = sprintf('Product sitemaps have been generated in %f seconds. Summary %d sitemaps.', Debugger::timer(), $sitemapCount);
        //$output->writeln($message);
        //$this->logger->addInfo($message);
        return 0;
    }

}