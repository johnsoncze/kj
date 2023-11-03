<?php

declare(strict_types = 1);

namespace App\Periskop\Xml;

use App\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class AbstractXmlCommand extends BaseCommand
{


    /** @var string arguments */
    const FILE_ARGUMENT = 'file';



    protected function configure()
    {
        parent::configure();
        $this->addArgument(self::FILE_ARGUMENT, InputArgument::REQUIRED, 'Set absolute path with file.');
    }



    /**
     * Get file from absolute path.
     * @param $filePath string
     * @return \SimpleXMLElement
     * @throws \InvalidArgumentException in case of the file does not exist
     */
    protected function getFile(string $filePath) : \SimpleXMLElement
    {
        if (is_file($filePath) !== TRUE) {
           throw new \InvalidArgumentException(sprintf('Soubor \'%s\' neexistuje.', $filePath));
        }

        if (!$xml = @simplexml_load_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('Nepodařilo se načíst soubor \'%s\' pro import.', $filePath));
        }
        return $xml;
    }



    /**
     * Check required elements.
     * @param $node \SimpleXMLElement
     * @param $output OutputInterface
     * @param $requiredElements array
     * @return bool
     */
    protected function checkRequiredElements(\SimpleXMLElement $node,
                                             OutputInterface $output,
                                             array $requiredElements = []) : bool
    {
		$missingElements = [];

        foreach ($requiredElements as $el) {
            if (!$node->xpath('./' . $el)) {
            	$missingElements[] = $el;
            }
		}

		if ($missingElements) {
        	$message = sprintf('Chybí elementy: \'%s\'', implode('\',\'', $missingElements));
        	$this->writeErrorMessage($output, $message, [
        		'class' => get_class($this),
				'node' => $node,
			]);
		}
		
		return !$missingElements;
    }
}