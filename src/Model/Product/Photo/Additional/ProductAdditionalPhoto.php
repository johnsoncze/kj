<?php

declare(strict_types = 1);

namespace App\Product\AdditionalPhoto;

use App\AddDateTrait;
use App\BaseEntity;
use App\Product\Photo\IPhoto;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_additional_photo")
 *
 * @method setProductId($id)
 * @method getProductId()
 * @method setFileName($name)
 * @method getFileName()
 */
class ProductAdditionalPhoto extends BaseEntity implements IEntity, IPhoto
{


    use AddDateTrait;

    /**
     * @Column(name="pap_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="pap_product_id")
     */
    protected $productId;

    /**
     * @Column(name="pap_file_name")
     */
    protected $fileName;

    /**
     * @Column(name="pap_add_date")
     */
    protected $addDate;



    /**
	 * @inheritdoc
    */
	public function getPhotoName()
	{
		return $this->getFileName();
	}



	/**
	 * @inheritdoc
	*/
	public function getUploadFolder() : string
	{
		return sprintf('products/%s', $this->getProductId());
	}


}