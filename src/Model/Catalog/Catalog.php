<?php

declare(strict_types = 1);

namespace App\Catalog;

use App\AddDateTrait;
use App\BaseEntity;
use App\IPublication;
use App\PublicationTrait;
use App\StateTrait;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslatable;
use Ricaefeliz\Mappero\Translation\TranslationTrait;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="catalog")
 *
 * @method setType($type)
 * @method getType()
 * @method setPhoto($photo)
 * @method getPhoto()
 * @method setSort($sort)
 */
class Catalog extends BaseEntity implements IEntity, IPublication, ITranslatable
{


    use AddDateTrait;
    use PublicationTrait;
    use StateTrait;
    use TranslationTrait;

    /** @var string */
    const TYPE_EMPLOYEE = 'employee';
    const TYPE_JOB_VACANCY = 'job_vacancy';
    const TYPE_REPRESENTATIVE_CATALOG = 'representative_catalog';


    /**
     * @Column(name="ctg_id", key="Primary")
     */
    protected $id;

    /**
     * @Translation
     * @OneToMany(entity="\App\Catalog\Translation\CatalogTranslation")
     */
    protected $translations;

    /**
     * @Column(name="ctg_type")
     */
    protected $type;

    /**
     * @Column(name="ctg_photo")
     */
    protected $photo;

    /**
     * @Column(name="ctg_sort")
     */
    protected $sort;

    /**
     * @Column(name="ctg_state")
     */
    protected $state;

    /**
     * @Column(name="ctg_add_date")
     */
    protected $addDate;


    /** @var array */
    protected static $types = [
        self::TYPE_EMPLOYEE => [
            'key' => self::TYPE_EMPLOYEE,
            'translation' => 'Zaměstnanci',
        ],
        self::TYPE_JOB_VACANCY => [
            'key' => self::TYPE_JOB_VACANCY,
            'translation' => 'Volné pracovní pozice',
        ],
        self::TYPE_REPRESENTATIVE_CATALOG => [
            'key' => self::TYPE_REPRESENTATIVE_CATALOG,
            'translation' => 'Reprezentativní katalogy',
        ],
    ];



    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort ?: time();
    }



    /**
     * @return self
    */
    public function deletePhoto() : self
    {
        $this->setPhoto(NULL);
        return $this;
    }



    /**
     * @return array
     */
    public static function getTypes() : array
    {
        return self::$types;
    }



    /**
     * @param $catalog Catalog
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getUploadFolder(Catalog $catalog) : string
    {
        $catalogId = $catalog->getId();
        if ($catalogId === NULL) {
            throw new \InvalidArgumentException('Missing catalog id.');
        }
        return sprintf('catalog/%d', $catalogId);
    }
}