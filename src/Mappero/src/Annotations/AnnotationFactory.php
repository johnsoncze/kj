<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Annotations;

use App\NObject;
use Nette\Reflection\ClassType;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class AnnotationFactory extends NObject
{


    /** @var PropertyFactory */
    private $propertyFactory;



    public function __construct()
    {
        $this->propertyFactory = new PropertyFactory(new RelationFactory($this));
    }



    /**
     * @param string $entityName
     * @return Annotation
	 * @throws \InvalidArgumentException
     */
    public function create(string $entityName) : Annotation
    {
        $annotation = new Annotation($entityName);
        $entityReflection = new ClassType($entityName);
        $tableFactory = new TableFactory();
        $propertyFactory = $this->propertyFactory;

        try {

            //Table
            if ($table = $entityReflection->getAnnotation(Table::TABLE)) {
                $table = $tableFactory->create($table);
                $annotation->setTable($table);
            } else {
            	throw new \InvalidArgumentException(sprintf('Missing table for %s entity.', $entityName));
			}

            //Properties
            $i = 0;
            foreach ($entityReflection->getProperties() as $property) {

                $propertyObject = $propertyFactory->create($property, $entityName);

                //first property of default table must be id
                if ($i === 0 && $table->getType() === Table::DEFAULT_TYPE && $propertyObject->getName() !== "id") {
                    $this->exception("first property must be id", $entityName);
                }
                $i++;

                if ($propertyObject) {
                    if (($column = $propertyObject->getColumn()) instanceof Column
                        && $column->isPrimary()
                    ) {
                        $annotation->setPrimaryProperty($propertyObject);
                    }
                    $annotation->addProperty($propertyObject);
                }
            }

            return $annotation;
        } catch (ColumnFactoryException $exception) {
            $this->exception($exception->getMessage(), $entityName);
        } catch (TableFactoryException $exception) {
            $this->exception($exception->getMessage(), $entityName);
        } catch (AnnotationCheckException $exception) {
            $this->exception($exception->getMessage(), $entityName);
        }
    }



    /**
     * @param $message string
     * @param $entity string
     * @throws AnnotationFactoryException
     */
    protected function exception(string $message, string $entity)
    {
        throw new AnnotationFactoryException(sprintf('For entity "%s" %s.', $entity, $message));
    }
}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class AnnotationFactoryException extends \Exception
{


}