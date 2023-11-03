<?php

declare(strict_types = 1);

namespace App\Periskop\Xml;

use App\BaseCommand;
use App\Environment\Environment;
use App\Periskop\Customer\Import\CustomerImportCommand;
use App\Periskop\Order\StateImportCommand;
use App\Periskop\Product\Import\ProductImportCommand;
use App\Periskop\Product\Import\ProductStockImportCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tracy\Debugger;


/**
 * Command which is searching a new xml files for import.
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ImportXmlCommand extends BaseCommand
{


    protected function configure()
    {
        parent::configure();
        $this->setName('periskop:xml:import')
            ->setDescription('Import XML files which was exported from Periskop.');
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $productImportCommand = $this->getApplication()->find(ProductImportCommand::COMMAND);
        $productStockImportCommand = $this->getApplication()->find(ProductStockImportCommand::COMMAND);
        $customerImportCommand = $this->getApplication()->find(CustomerImportCommand::COMMAND);
        $orderStateImportCommand = $this->getApplication()->find(StateImportCommand::COMMAND);

        $environment = Environment::create();
        Debugger::timer();
        $outputStyle = new SymfonyStyle($input, $output);

        try {
            $folder = $this->getFolder();
        } catch (\InvalidArgumentException $exception) {
        	$this->writeErrorMessage($output, $exception->getMessage());
            return 1;
        }

        $importedFileCount = 0;
        $importedFiles = [];

        if($environment->isLocal()) {
            $this->recreateOkFiles($folder);
        }
        $filePattern = sprintf('%s/ES*.xml.ok', $folder); //file extension 'ok' means file which was imported correctly
        $files = glob($filePattern);

        $this->writeInfoMessage($output, sprintf('Found new xml files for import: %d. Files: %s', count($files), implode(', ', $files)));

        //dd($filePattern);

        foreach ($files ?: [] as $file) {
            try {
                $fileOkObject = new \SplFileInfo($file);
                $filePath = str_replace('.ok', '', $fileOkObject->getRealPath());
                $this->copyFile($fileOkObject); //because more running commands will not import same file
                $movedFileOkObject = $this->moveFile($fileOkObject); //because more running commands will not import same file
                $fileObject = new \SplFileInfo($filePath);
                if ($fileObject->isFile() !== TRUE){
                	$this->writeErrorMessage($output, sprintf('The file \'%s\' for import not found.', $filePath));
                    continue;
                }

                // check that the iterated file still exists (script runs might overlap)
                if(!$fileObject->getRealPath()) {
                    $this->writeNoticeMessage($output, 'File no longer exists, skipping: ' . $fileObject->getFilename());
                    continue;
                }

                $this->copyFile($fileObject); //because more running commands will not import same file
                $movedFile = $this->moveFile($fileObject);

                //import order state
                if (preg_match('/ES\d+O\.xml/', $fileObject->getBasename())) {
                    //$outputStyle->section('Order state import - ' . $movedFile);
                    //$orderStateInput = new ArrayInput(['file' => $movedFile]);
                    //$orderStateImportCommand->run($orderStateInput, $output);
                }

                //import product stock
                elseif (preg_match('/ES\d+S\.xml/', $fileObject->getBasename())) {
                    $outputStyle->section('Product stock import - ' . $movedFile);
                    $productStockInput = new ArrayInput(['file' => $movedFile]);
                    $productStockImportCommand->run($productStockInput, $outputStyle);
                }

                else {
                    //import products
                    $outputStyle->section('Product import - ' . $movedFile);
                    $productInput = new ArrayInput(['file' => $movedFile]);
                    $productImportCommand->run($productInput, $output);

                    //import customers
                    $outputStyle->section('Customer import - ' . $movedFile);
                    $customerInput = new ArrayInput(['file' => $movedFile]);
                    $customerImportCommand->run($customerInput, $output);
                }

                $importedFileCount++;
                $importedFiles[] = $filePath;
                unlink($movedFile);
                unlink($movedFileOkObject);
            } catch (\InvalidArgumentException $exception) {
            	$this->writeErrorMessage($output, $exception->getMessage());
            }
        }

        $this->writeInfoMessage($output, sprintf('Import of xml files has been finished in time %s. Imported file count: %d. Files: %s', Debugger::timer(), $importedFileCount, $importedFiles ? implode(', ', $importedFiles) : '-'));
        return 0;
    }



    /**
     * Get folder with files.
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getFolder() : string
    {
        $parameters = $this->container->getParameters();
        if (!isset($parameters['periskop']['ftp']['path'])) {
            throw new \InvalidArgumentException('Missing definition of folder with files.');
        }
        return $parameters['periskop']['ftp']['path'];
    }



    /**
     * Move file to processing directory.
     * @param $file \SplFileInfo
     * @return string new absolute file path
     * @throws \InvalidArgumentException does not exist processing folder
     */
    private function moveFile(\SplFileInfo $file) : string
    {
        $processingFolder = 'processing';
        $processingFolderPath = sprintf('%s/%s', $file->getPath(), $processingFolder);
        if (!@mkdir($processingFolderPath, 0755, TRUE) && !is_dir($processingFolderPath)) {
            throw new \InvalidArgumentException(sprintf('The processing folder %s does not exist.', $processingFolderPath));
        }
        $fileName = sprintf('%s_%s.xml', $file->getBasename('.xml'), time());
        $movedFilePath = sprintf('%s/%s', $processingFolderPath, $fileName);
        $fileRealPath = $file->getRealPath();
        if(!$fileRealPath) {
            throw new \InvalidArgumentException('Import XML file - unable to resolve realPath: ' . $file->getFilename());
        }
        if (!file_exists($fileRealPath) || (rename($fileRealPath, $movedFilePath) !== TRUE)) {
            throw new \InvalidArgumentException(sprintf('The file %s has not been moved.', $movedFilePath));
        }
        return $movedFilePath;
    }

    /**
     * Copy file to backup directory.
     * @param $file \SplFileInfo
     * @return string copyied absolute file path
     * @throws \InvalidArgumentException does not exist processing folder
     */
    private function copyFile(\SplFileInfo $file) : string
    {
        $processingFolder = 'backup';
        $processingFolderPath = sprintf('%s/%s', $file->getPath(), $processingFolder);
        if (!@mkdir($processingFolderPath, 0755, TRUE) && !is_dir($processingFolderPath)) {
            throw new \InvalidArgumentException(sprintf('The processing folder %s does not exist.', $processingFolderPath));
        }
        $copiedFileName = sprintf('%s_%s.xml', $file->getBasename('.xml'), time());
        $copiedFilePath = sprintf('%s/%s', $processingFolderPath, $copiedFileName);
        $fileRealPath = $file->getRealPath();
        if(!$fileRealPath) {
            throw new \InvalidArgumentException('Import XML file - unable to resolve realPath: ' . $file->getFilename());
        }
        if (!file_exists($fileRealPath) || (copy($fileRealPath, $copiedFilePath) !== TRUE)) {
            throw new \InvalidArgumentException(sprintf('The file %s has not been copied.', $copiedFilePath));
        }
        return $copiedFilePath;
    }

    /**
     * @param string $folder
     * @return void
     */
    private function recreateOkFiles(string $folder)
    {
        $filePattern = sprintf('%s/ES*.xml', $folder);
        $files = glob($filePattern);
        foreach($files as $xmlFile) {
            touch(
                preg_replace(
                    '/(\.xml)$/',
                    '.xml.ok',
                    $xmlFile
                )
            );
        }
    }
}
