<?php

namespace Ricaefeliz\Mappero\Bridges\NetteDatabase;

use Ricaefeliz\Mappero\Annotations\Annotation;
use Ricaefeliz\Mappero\Bridges\NetteDatabase\Builders\QueryBuilder;
use Ricaefeliz\Mappero\Bridges\NetteDatabase\Builders\FilterBuilder;
use Nette\Database\Context;
use Nette\Database\Table\IRow;
use App\NObject;
use Ricaefeliz\Mappero\Helpers\Columns;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class NetteDatabase extends NObject
{


    /** @var Context */
    protected $context;

    /** @var FilterBuilder */
    protected $filterBuilder;

    /** @var QueryBuilder */
    protected $queryBuilder;



    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->queryBuilder = new QueryBuilder();
        $this->filterBuilder = new FilterBuilder();
    }



    /**
     * @param $table string
     * @param $data array
     * @return IRow|int|bool last insert id or first insert id when you saved more than one row
     */
    public function insert($table, $data)
    {
        return $this->context->table($table)->insert($data);
    }



    /**
     * @param $table string
     * @param $primaryColumn string
     * @param $primaryKey string
     * @param $data array
     * @return mixed
     */
    public function update($table, $primaryColumn, $primaryKey, $data)
    {
        return $this->context->table($table)
            ->where($primaryColumn . " = ?", $primaryKey)
            ->update($data);
    }



    /**
     * @param Annotation $annotation
     * @param array|NULL $filters
     * @return array|\Nette\Database\Table\IRow[]|\Nette\Database\Table\Selection
     */
    public function findBy(Annotation $annotation, array $filters = NULL)
    {
        $query = $this->context->table($annotation->getTable()->getName());

        if ($filters) {
            $this->filterBuilder->apply($query, $filters, $annotation);
        }
        if (isset($filters['join'])) {
            $joinQuery = $this->createJoinSql($filters['join']);
            $sql = str_replace("FROM `{$annotation->getTable()->getName()}`", "FROM `{$annotation->getTable()->getName()}` $joinQuery", $query->getSql());
            if (isset($filters['columns'])){
                $sql = str_replace('SELECT *', 'SELECT ' . implode(',', $filters['columns']), $sql);
            }
            return $this->context->queryArgs($sql, $query->getSqlBuilder()->getParameters())->fetchAll();
        }
        $columns = Columns::getColumns($annotation, (isset($filters["columns"]) ? $filters["columns"] : []));
        $query->select($this->queryBuilder->getColumnsQuery($columns));
        return $query->fetchAll();
    }



    /**
     * @param $annotation Annotation
     * @param array|NULL $filters
     * @return bool|mixed|IRow
     */
    public function findOneBy(Annotation $annotation, array $filters = NULL)
    {
        $query = $this->context->table($annotation->getTable()->getName());
        if ($filters) {
            $this->filterBuilder->apply($query, $filters, $annotation);
        }
        $columns = Columns::getColumns($annotation, (isset($filters["columns"]) ? $filters["columns"] : NULL));
        $query->select($this->queryBuilder->getColumnsQuery($columns));
        return $query->fetch();
    }



    /**
     * @param $annotation Annotation
     * @param array|NULL $filters
     * @return int
     */
    public function count(Annotation $annotation, array $filters = NULL)
    {
        $query = $this->context->table($annotation->getTable()->getName());
        if ($filters) {
            $this->filterBuilder->apply($query, $filters, $annotation);
        }
        if (isset($filters['join'])) {
            $joinQuery = $this->createJoinSql($filters['join']);
            $sql = str_replace(["FROM `{$annotation->getTable()->getName()}`", 'SELECT *'], ["FROM `{$annotation->getTable()->getName()}` $joinQuery", 'SELECT COUNT(*)'], $query->getSql());
            return $this->context->queryArgs($sql, $query->getSqlBuilder()->getParameters())->fetchField();
        }
        return $query->count('*');
    }



    /**
     * @param $table string
     * @param $primaryColumn string
     * @param $primaryKey string
     * @return int
     */
    public function remove($table, $primaryColumn, $primaryKey)
    {
        return $this->context->table($table)
            ->where($primaryColumn . " ?", $primaryKey)
            ->delete();
    }



    /**
     * Create sql join query.
     * @param $joins array [['LEFT JOIN', 'table', 'column = column'],..]
     * @return string
     */
    protected function createJoinSql(array $joins) : string
    {
        $joinQuery = '';
        foreach ($joins as $join) {
            $joinQuery .= sprintf(' %s %s ON %s ', $join[0], $join[1], $join[2]);
        }
        return $joinQuery;
    }
}