<?php

namespace Ricaefeliz\Mappero\Bridges\NetteDatabase\Builders;

use Nette\Database\SqlLiteral;
use Nette\Database\Table\Selection;
use App\NObject;
use Ricaefeliz\Mappero\Annotations\Annotation;
use Ricaefeliz\Mappero\Annotations\Property;
use Ricaefeliz\Mappero\Exceptions\FilterBuilderException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class FilterBuilder extends NObject
{


    /**
     * @param $selection Selection
     * @param $filters array|null
     * @param $annotation Annotation
     * @return Selection
     */
    public function apply(Selection $selection, array $filters = NULL, Annotation $annotation)
    {
        if (isset($filters["where"])) {
            $this->applyWhere($selection, $filters["where"], $annotation);
        }
        if (isset($filters['whereOr'])) {
            $this->applyWhereOr($selection, $filters['whereOr'], $annotation);
        }
        if (isset($filters["limit"])) {
            $offset = isset($filters["offset"]) ? $filters["offset"] : NULL;
            $this->applyLimit($selection, $filters["limit"], $offset);
        }
        if (isset($filters["sort"])) {
            $this->applySort($selection, $filters["sort"], $annotation);
        }
        if (isset($filters["group"])) {
            $this->applyGroupBy($selection, $filters["group"], $annotation);
        }
        if (isset($filters["having"])) {
            $this->applyHaving($selection, $filters["having"], $annotation);
        }
        return $selection;
    }



    /**
     * @param $selection Selection
     * @param array $where
     * @param $annotation Annotation
     * @return Selection
     * @throws \InvalidArgumentException
     */
    protected function applyWhere(Selection $selection, array $where, Annotation $annotation)
    {
        foreach ($where as $condition) {
            if (class_exists("\\Grido\\Components\\Filters\\Condition") && $condition instanceof \Grido\Components\Filters\Condition) {
                if ($property = $annotation->getPropertyByName($condition->getColumn()[0])) {
                    $condition->setColumn($property->getColumn()->getName());
                }
                call_user_func_array(array($selection, 'where'), $condition->__toArray());
            } //For condition like ["column ? OR column NOT ? && column != ?", [NULL, NULL, 20]]
            elseif (is_countable($condition) && count($condition) == 2) {
                $selection->where($condition[0], $condition[1]);
            } //For subquery for example..
            elseif (is_string($condition)) {
                $selection->where($condition);
            } //where in another sql query
            elseif (in_array($condition[1], ['IN.SQL', 'NOTIN.SQL', '=SQL'], TRUE)) {
            	$operator = str_replace(['NOTIN.SQL', 'IN.SQL', '=SQL'], ['NOT IN', 'IN', '='], $condition[1]);
                $property = $annotation->getPropertyByName($condition[0]);
                $selection->where($property->getColumn()->getName() . ' ' . $operator . ' ?', new SqlLiteral($condition[2]));
            } //For condition like ["column", "!=", "value"]
            elseif ($columnDefinition = $condition[0]) {
                $property = $annotation->getPropertyByName($columnDefinition);
                $propertyQuery = $property ? $property->getColumn()->getName() : $columnDefinition;
                $selection->where("{$propertyQuery} {$condition[1]} ?", $condition[2]);
            }
        }

        return $selection;
    }



    /**
     * @param $selection Selection
     * @param $where array
     * @param $annotation Annotation
     * @return Selection
     */
    protected function applyWhereOr(Selection $selection, array $where, Annotation $annotation) : Selection
    {
        $_where = [];
        foreach ($where as $condition) {
            list($property, $operator, $value) = $condition;
            $propertyObject = $annotation->getPropertyByName($property);
            $value = $operator === 'IN.SQL' ? new SqlLiteral($value) : $value;
            $o = str_replace('IN.SQL', 'IN', $operator);
            $_where[sprintf('%s %s', $propertyObject ? $propertyObject->getColumn()->getName() : $property, $o)] = $value;
        }
        $selection->whereOr($_where);

        return $selection;
    }



    /**
     * @param $selection Selection
     * @param $limit int
     * @param $offset int|null
     * @return Selection
     */
    protected function applyLimit(Selection $selection, $limit, $offset = NULL)
    {
        $selection->limit($limit, $offset ?? NULL);
        return $selection;
    }



    /**
     * @param $selection Selection
     * @param $sort array
     * @param $annotation Annotation
     * @return Selection
     * @throws FilterBuilderException
     */
    protected function applySort(Selection $selection, array $sort, Annotation $annotation)
    {
        if (is_array($sort[0])) {
            $column = '';
            foreach ($sort[0] as $item) {
                if (preg_match('/LENGTH\((.*)\)/', $item, $matched) === 1) {
                	$propertyName = end($matched);
                    $property = $annotation->getPropertyByName($propertyName);
                    $column .= sprintf('LENGTH(%s)', $property ? $property->getColumn()->getName() : $propertyName);
                } else {
                    $property = $annotation->getPropertyByName($item);
                    $column .= $property ? $property->getColumn()->getName() : $item;
                }
                $column .= ',';
            }
            $column = rtrim($column, ',');
        } //for join table :table_name.column_name
        elseif (0 === strpos($sort[0], ':')) {
            $column = $sort[0];
        } elseif ($property = $annotation->getPropertyByName($sort[0])) {
            $column = $property->getColumn()->getName();
        } else {
            $column = $sort[0];
        }

        $by = $sort[1] ?? 'ASC';
        $selection->order($column . ' ' . $by);
        return $selection;
    }



    /**
     * @param Selection $selection
     * @param array $groupBy
     * @param Annotation $annotation
     * @return Selection
     */
    protected function applyGroupBy(Selection $selection, array $groupBy, Annotation $annotation)
    {
        $columns = "";
        foreach ($groupBy as $propertyName) {
            $property = $annotation->getPropertyByName($propertyName);
            $columns .= $property->getColumn()->getName();
        }
        $selection->group($columns);
        return $selection;
    }



    /**
     * @param Selection $selection
     * @param array $having
     * @param Annotation $annotation
     * @return Selection
     */
    protected function applyHaving(Selection $selection, array $having, Annotation $annotation)
    {
        foreach ($having as $h) {
            $propertyName = str_replace(["SUM", "COUNT", "(", ")"], "", $h[0]);
            $property = $annotation->getPropertyByName($propertyName); //find if is property or alias of some result in query
            $column = $property instanceof Property ? str_replace($propertyName, $property->getColumn()->getName(), $h[0]) : $h[0];
            $condition = $h[1];
            $value = $h[2];
            $selection->having("{$column} {$condition} ?", $value);
        }
        return $selection;
    }



    /**
     * @param $selection Selection
     * @param $sort array
     * @throws \Exception
     */
    protected function applyFieldSort(Selection $selection, array $sort)
    {
        throw new \Exception("Not implement yet");
    }
}