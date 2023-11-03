<?php

declare(strict_types = 1);

namespace App\Product\WeedingRing\Gender;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Gender
{


    /** @var string */
    const MALE = 'male';
    const FEMALE = 'female';

    /** @var string */
    protected $type;

    /** @var array */
    protected static $types = [
        self::MALE => [
            'key' => self::MALE,
            'translation' => 'Pánský',
            'translationKey' => 'product.gender.male.label'
        ],
        self::FEMALE => [
            'key' => self::FEMALE,
            'translation' => 'Dámský',
            'translationKey' => 'product.gender.female.label',
        ],
    ];



    public function __construct(string $type)
    {
        $this->type = $type;
    }



    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }



    /**
     * @return array
     */
    public function getTypeValues() : array
    {
        return self::getTypes()[$this->getType()];
    }



    /**
     * @return array
     */
    public static function getTypes() : array
    {
        return self::$types;
    }
}