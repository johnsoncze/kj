<?php

declare(strict_types = 1);

namespace App\Catalog\Translation;

use App\BaseEntity;
use App\LanguageTrait;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="catalog_translation")
 *
 * @method setCatalogId($id)
 * @method getCatalogId()
 * @method setTitle($title)
 * @method getTitle()
 * @method setSubtitle($subtitle)
 * @method getSubtitle()
 * @method setText($text)
 * @method getText()
 * @method setAbout($about)
 * @method getAbout()
 * @method setFile($file)
 * @method getFile()
 * @method setFileSize($size)
 * @method getFileSize()
 */
class CatalogTranslation extends BaseEntity implements IEntity, ITranslation
{


    use LanguageTrait;

    /**
     * @Column(name="ctgt_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="ctgt_catalog_id")
     */
    protected $catalogId;

    /**
     * @Column(name="ctgt_language_id")
     */
    protected $languageId;

    /**
     * @Column(name="ctgt_title")
     */
    protected $title;

    /**
     * @Column(name="ctgt_subtitle")
     */
    protected $subtitle;

    /**
     * @Column(name="ctgt_text")
     */
    protected $text;

    /**
     * @Column(name="ctgt_about")
     */
    protected $about;

    /**
     * @Column(name="ctgt_file")
     */
    protected $file;

    /**
     * @Column(name="ctgt_file_size")
     */
    protected $fileSize;



    /**
     * Delete file from object.
     * @return self
     */
    public function deleteFile() : self
    {
        $this->setFile(NULL);
        $this->setFileSize(NULL);
        return $this;
    }
}