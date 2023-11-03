<?php

declare(strict_types = 1);

namespace App\Category\Product\Related;

use App\BaseEntity;
use App\EntitySortTrait;
use App\Helpers\Arrays;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="category_related_product")
 *
 * @method setCategoryId($id)
 * @method getCategoryId()
 * @method setProductId($id)
 * @method getProductId()
 * @method setType($type)
 * @method getType()
 */
class Product extends BaseEntity implements IEntity
{


	/** @var string */
	const TYPE_HOMEPAGE = 'homepage';
	const TYPE_IMAGE_TEMPLATE = 'image_template';
    const TYPE_SORTING_PRODUCT = 'sorting_product';

	use EntitySortTrait;

	/**
	 * @Column(name="clp_id", key="Primary")
	 */
	protected $id;

	/**
	 * @Column(name="clp_category_id")
	 */
	protected $categoryId;

	/**
	 * @Column(name="clp_product_id")
	 */
	protected $productId;

	/**
	 * @Column(name="clp_type")
	 */
	protected $type;

	/**
	 * @Column(name="clp_sort")
	 */
	protected $sort;

	/** @var array */
	protected static $types = [
		self::TYPE_HOMEPAGE => [
			'key' => self::TYPE_HOMEPAGE,
			'translation' => 'Kolekce na hlavní stránce',
		],
		self::TYPE_IMAGE_TEMPLATE => [
			'key' => self::TYPE_IMAGE_TEMPLATE,
			'translation' => 'Image šablona',
		],
        self::TYPE_SORTING_PRODUCT => [
            'key' => self::TYPE_SORTING_PRODUCT,
            'translation' => 'Prioritní řazení pro algoritmus',
        ],
	];



	/**
	 * @param $type string
	 * @return array
	 * @throws \InvalidArgumentException
	*/
	public static function getTypeValue(string $type) : array
	{
		$types = self::getTypes();
		$typeValue = $types[$type] ?? NULL;
		if ($typeValue === NULL) {
			throw new \InvalidArgumentException(sprintf('Unknown type \'%s\'.', $type));
		}
		return $typeValue;
	}



	/**
	 * @return array
	 */
	public static function getTypes() : array
	{
		return self::$types;
	}



	/**
	 * @return array
	 */
	public static function getTypeList() : array
	{
		$types = self::getTypes();
		return Arrays::toPair($types, 'key', 'translation');
	}
}