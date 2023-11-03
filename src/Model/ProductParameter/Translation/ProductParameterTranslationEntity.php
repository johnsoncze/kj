<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\BaseEntity;
use App\LanguageTrait;
use Nette\Utils\Strings;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Entities\Traits\UrlTrait;
use Ricaefeliz\Mappero\Translation\ITranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_parameter_translation")
 *
 * @method setProductParameterId($id)
 * @method getProductParameterId()
 * @method getValue()
 * @method setAddDate($date)
 * @method getAddDate()
 */
class ProductParameterTranslationEntity extends BaseEntity implements IEntity, ITranslation
{


    use LanguageTrait;
    use UrlTrait;


    /**
     * @Column(name="ppt_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="ppt_language_id")
     */
    protected $languageId;

    /**
     * @Column(name="ppt_product_parameter_id")
     */
    protected $productParameterId;

    /**
     * @Column(name="ppt_value")
     */
    protected $value;

    /**
     * @Column(name="ppt_url")
     */
    protected $url;

    /**
     * @Column(name="ppt_add_date")
     */
    protected $addDate;



    /**
     * Setter for 'value' property.
     * @param $value string
     * @return self
     */
    public function setValue(string $value) : self
    {
        $this->value = Strings::firstUpper($value);
        return $this;
    }
}