<?php

namespace App\ProductParameterGroup;

use App\BaseEntity;
use App\LanguageTrait;
use Nette\Utils\Strings;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_parameter_group_translation")
 *
 * @method setProductParameterGroupId($id)
 * @method getProductParameterGroupId()
 * @method setLanguageId($id)
 * @method getName()
 * @method getFiltrationTitle()
 * @method setHelp($help)
 * @method getHelp()
 * @method setAddDate($addDate)
 * @method getAddDate()
 */
class ProductParameterGroupTranslationEntity extends BaseEntity implements IEntity, ITranslation
{


    use LanguageTrait;

    /**
     * @Column(name="ppgt_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="ppgt_product_parameter_group_id")
     */
    protected $productParameterGroupId;

    /**
     * @Column(name="ppgt_language_id")
     */
    protected $languageId;

    /**
     * @Column(name="ppgt_name")
     */
    protected $name;

    /**
     * @Column(name="ppgt_filtration_title")
     */
    protected $filtrationTitle;

    /**
     * @Column(name="ppgt_help")
    */
    protected $help;

    /**
     * @Column(name="ppgt_add_date")
     */
    protected $addDate;



    /**
     * @return mixed
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }



    /**
     * Setter for 'name' property.
     * @param $name string
     * @return self
     */
    public function setName(string $name) : self
    {
        $this->name = Strings::firstUpper($name);
        return $this;
    }



    /**
     * Setter for 'filtrationTitle' property.
     * @param $title string
     * @return self
     */
    public function setFiltrationTitle(string $title) : self
    {
        $this->filtrationTitle = Strings::firstUpper($title);
        return $this;
    }
}