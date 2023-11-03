<?php

namespace App\Helpers;

use Ricaefeliz\Mappero\Entities\IEntity;
use App\NObject;
use Nette\StaticClass;


/**
 * Helper for work with entities
 *
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Entities extends NObject
{


    use StaticClass;

    /** @var int const for ::searchValues() method */
    const VALUE_FOUND = 1;
    const VALUE_NOT_FOUND = 2;
    const ENTITY_WITHOUT_VALUE = 3;

    /** @var int */
    const DEFAULT_FLAG = 0;
    const VALUE_TRANSLATION_FLAG = 1;



    /**
     * @param $entities IEntity[]|null
     * @return array
     */
    public static function toArray(array $entities = null)
    {
        $data = [];
        foreach ($entities ? $entities : [] as $entity) {
            $data[$entity->getId()] = $entity->toArray();
        }
        return $data;
    }



    /**
     * @param $entities array|null
     * @param $key string
     * @param $value string
     * @param $flag int
     * @return array
     * @throws EntitiesException
     */
    public static function toPair(array $entities = null, $key, $value, int $flag = self::DEFAULT_FLAG)
    {
        //check flag
        $flags = [self::DEFAULT_FLAG, self::VALUE_TRANSLATION_FLAG];
        if (!in_array($flag, $flags, TRUE)) {
            throw new EntitiesException(sprintf("Unknown flag '%s'. You can set '%s' flags.", $flag, implode(",", $flags)));
        }

        $data = [];
        foreach ($entities ? $entities : [] as $k => $v) {
            switch ($flag) {
                case self::DEFAULT_FLAG:
                    $val = $v->{self::getGetterMethodName($value)}();
                    break;
                case self::VALUE_TRANSLATION_FLAG:
                    $val = $v->getTranslation()->{self::getGetterMethodName($value)}();
                    break;

            }
            $data[$v->{self::getGetterMethodName($key)}()] = $val;
        }
        return $data;
    }



    /**
     * @param array|null $entities
     * @param $property string
     * @return array
     */
    public static function getProperty(array $entities = null, $property)
    {
        $data = [];
        foreach ($entities ? $entities : [] as $k => $v) {
            $data[] = $v->{"get" . ucfirst($property)}();
        }
        return $data;
    }



	/**
	 * @param array|null $entities
	 * @param $property string
	 * @return array
	 */
	public static function getNotNullProperty(array $entities = null, $property)
	{
		$data = [];
		foreach ($entities ? $entities : [] as $k => $v) {
			$value = $v->{"get" . ucfirst($property)}();
			if ($value !== NULL) {
				$data[] = $value;
			}
		}
		return $data;
	}



    /**
     * @param $entities IEntity[]
     * @param $method string
     * @return array
    */
    public static function getValueFromMethod(array $entities, string $method) : array
    {
        $data = [];
        foreach ($entities ? $entities : [] as $k => $v) {
            $data[] = $v->{$method}();
        }
        return $data;
    }



    /**
     * @param array $entities
     * @param string $property
     * @return array
     */
    public static function getUniqueProperty(array $entities, string $property) : array
    {
        $data = [];
        foreach ($entities as $entity) {
            $value = $entity->{self::getGetterMethodName($property)}();
            if ($value !== NULL && !in_array($value, $data)) {
                $data[] = $value;
            }
        }
        return $data;
    }



    /**
     * @param array $entities
     * @param $property
     * @param $value
     * @return int|null|string
     */
    public static function getKey(array $entities, $property, $value)
    {
        foreach ($entities ? $entities : [] as $key => $entity) {
            if ($entity->{"get" . ucfirst($property)}() == $value) {
                return $key;
            }
        }
        return null;
    }



    /**
     * Search values in entities
     * @param array|null $entities
     * @param array|null $values
     * @param $property
     * @return array
     */
    public static function searchValues(array $entities = null, array $values, $property)
    {
        $data = [];
        foreach ($values ? $values : [] as $value) {
            $key = Entities::getKey($entities ? $entities : [], $property, $value);
            if ($key === NULL) {
                $data[self::VALUE_NOT_FOUND][] = $value;
            } else {
                $data[self::VALUE_FOUND][] = $entities[$key];
                unset($entities[$key]);
            }
        }
        if ($entities) {
            $data[self::ENTITY_WITHOUT_VALUE] = $entities;
        }
        return $data;
    }



    /**
     * @param array $entities [id => entity, id => entity..]
     * @param array $pattern id order consecutively
     * @param bool $set
     * @return array
     * @throws EntitiesException
     */
    public static function sortById(array $entities, array $pattern, $set = TRUE)
    {
        $sorted = [];
        $i = 1;
        foreach ($pattern as $position => $id) {
            if (!isset($entities[$id])) {
                throw new EntitiesException("Missing entity with id '{$id}'.");
            }
            $entity = $entities[$id];
            if ($set === TRUE) {
                if (!$entity instanceof IEntitySort) {
                    throw new EntitiesException("Entity must be instance of '" . IEntitySort::class . "' for set sort.");
                }
                $entity->setSort($i);
                $i++;
            }
            $sorted[$entity->getId()] = $entity;
            unset($entities[$id]);
        }
        if ($entities) {
            throw new EntitiesException("Is not defined order for some entities. " . var_dump($entities));
        }
        return $sorted;
    }



    /**
     * @param IEntity[] $entities
     * @return array
     * @throws EntitiesException
     */
    public static function hasId(array $entities)
    {
        foreach ($entities as $entity) {
            if (!$entity instanceof IEntity) {
                throw new EntitiesException("Entity must be instance of '" . IEntity::class . "'. Object '" . get_class($entity) . "' given.");
            } elseif (!$entity->getId()) {
                throw new EntitiesException("Entity has not the id.");
            }
        }
        return $entities;
    }



    /**
     * Set value as key.
     * @param $entities IEntity[]
     * @param $property string
     * @return IEntity[]
     * @throws \InvalidArgumentException duplicate value
     */
    public static function setValueAsKey(array $entities, string $property) : array
    {
        $array = [];
        foreach ($entities as $entity) {
            $method = self::getGetterMethodName($property);
            $value = $entity->{$method}();
            if (isset($array[$value])) {
                throw new \InvalidArgumentException(sprintf('Duplicate value \'%s\'.', $value));
            }
            $array[$value] = $entity;
        }
        return $array;
    }



    /**
     * @param string $property
     * @return string
     */
    public static function getGetterMethodName(string $property) : string
    {
        return "get" . ucfirst($property);
    }



    /**
     * @param $entities IEntity[]
     * @return IEntity[]
     * @throws \InvalidArgumentException
     */
    public static function setIdAsKey(array $entities) : array
    {
        $arr = [];
        foreach ($entities as $entity) {
            if (isset($arr[$entity->getId()])) {
                throw new \InvalidArgumentException(sprintf('Duplicate entity with same id %d.', $entity->getId()));
            }
            $arr[$entity->getId()] = $entity;
        }
        return $arr;
    }



    /**
     * @param $entities IEntity[]
     * @param $property string
     * @return array
    */
    public static function toSegment(array $entities, string $property) : array
    {
        $arr = [];
        foreach ($entities as $entity) {
            $key = $entity->{self::getGetterMethodName($property)}();
            $arr[$key][] = $entity;
        }
        return $arr;
    }
}