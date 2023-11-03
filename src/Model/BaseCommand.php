<?php

declare(strict_types = 1);

namespace App;

use Kdyby\Monolog\Logger;
use Nette\Database\Context;
use Nette\DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class BaseCommand extends Command
{


    /** @var Container */
    protected $container;

    /** @var Context */
    protected $database;

    /** @var Logger */
    protected $logger;



    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->container = $this->getHelper('container');
        $this->database = $this->container->getByType(Context::class);
        $this->logger = $this->container->getByType(Logger::class);
    }



    /**
     * Write and log an info message.
     * @param $output OutputInterface
     * @param $message string
     * @param $params array
     * @return string message
     */
    protected function writeInfoMessage(OutputInterface $output, string $message, array $params = []) : string
    {
        $this->logger->addInfo($message, $params);
        $output->writeln($message);
        return $message;
    }



    /**
	 * @param $output OutputInterface
	 * @param $message string
	 * @param $params array
	 * @return string
    */
    protected function writeNoticeMessage(OutputInterface $output, string $message, array $params = []) : string
	{
		$this->logger->addNotice($message, $params);
		$output->writeln($message);
		return $message;
	}



    /**
     * Write and log a warning message.
     * @param $output OutputInterface
     * @param $message string
     * @param $params array
     * @return string
     */
    protected function writeWarningMessage(OutputInterface $output, string $message, array $params = []) : string
    {
        $this->logger->addWarning($message, $params);
        $output->writeln($message);
        return $message;
    }



    /**
	 * @param $output OutputInterface
	 * @param $message string
	 * @param $params array
	 * @return string
    */
    protected function writeErrorMessage(OutputInterface $output, string $message, array $params = []) : string
	{
		$this->logger->addError($message, $params);
		$output->writeln($message);
		return $message;
	}
}