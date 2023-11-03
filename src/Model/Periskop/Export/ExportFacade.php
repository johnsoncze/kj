<?php

declare(strict_types = 1);

namespace App\Periskop\Export;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ExportFacade
{


    /** @var ExportRepository */
    private $exportRepo;



    public function __construct(ExportRepository $exportRepository)
    {
        $this->exportRepo = $exportRepository;
    }



    /**
     * @param $file string
     * @param $type string
     * @return Export
     */
    public function add(string $file, string $type) : Export
    {
        $export = new Export();
        $export->setFile($file);
        $export->setType($type);
        $export->setAddDate(new \DateTime());
        $this->exportRepo->save($export);
        return $export;
    }
}