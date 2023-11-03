<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Annotations;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @method getName()
 * @method Column getColumn()
 * @method getTranslation()
 */
class Property extends NObject
{


    /** @var string */
    protected $name;

    /** @var Column|null */
    protected $column;

    /** @var Relation|null */
    protected $relation;

    /** @var Translation|null */
    protected $translation;



    public function __construct(string $name)
    {
        $this->name = $name;
    }



    /**
     * @param $column
     * @return $this
     * @throws PropertyException
     */
    public function setColumn(Column $column)
    {
        if ($this->relation) {
            throw new PropertyException("You can not set column when you set 
            relation for entity '" . $this->relation->getRelationEntity() . "'.");
        }
        $this->column = $column;
        return $this;
    }



    /**
     * @param Relation $relation
     * @return $this
     * @throws PropertyException
     */
    public function setRelation(Relation $relation)
    {
        if ($this->column) {
            throw new PropertyException("You can not set relation when you 
            set column '{$this->column->getName()}'.");
        }
        $this->relation = $relation;
        return $this;
    }



    /**
     * @return Relation|null
     */
    public function getRelation()
    {
        return $this->relation;
    }



    /**
     * @param Translation $translation
     * @return Property
     */
    public function setTranslation(Translation $translation) : self
    {
        $this->translation = $translation;
        return $this;
    }

}