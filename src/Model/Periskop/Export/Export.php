<?php

declare(strict_types = 1);

namespace App\Periskop\Export;

use App\AddDateTrait;
use App\BaseEntity;
use Nette\DI\Container;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="periskop_export")
 *
 * @method setType($type)
 * @method getType()
 * @method setFile($file)
 * @method getFile()
 * @method setAddDate()
 * @method getAddDate()
 */
class Export extends BaseEntity implements IEntity
{


    /** @var string types */
    const TYPE_CUSTOMER = 'customer';
    const TYPE_ORDER = 'order';
    const TYPE_DEMAND = 'demand';

    use AddDateTrait;

    /**
     * @Column(name="pe_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="pe_type")
     */
    protected $type;

    /**
     * @Column(name="pe_file")
     */
    protected $file;

    /**
     * @Column(name="pe_add_date")
     */
    protected $addDate;



    /**
     * @param $type string
     * @return string
     */
    public static function generateFileName(string $type) : string
    {
        $suffix = str_replace([self::TYPE_CUSTOMER, self::TYPE_ORDER], ['', 'O'], $type);
        return sprintf('ES%s%s.xml', (new \DateTime())->format('YmdHis'), $suffix);
    }



    /**
     * @param $container Container
     * @param $fileName string
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getAbsoluteFilePath(Container $container, string $fileName) : string
    {
        $parameters = $container->getParameters();
        $folder = $parameters['periskop']['ftp']['out'] ?? NULL;
        if ($folder === NULL) {
            throw new \InvalidArgumentException('Missing folder.');
        }
        return $folder . DIRECTORY_SEPARATOR . $fileName;
    }
}