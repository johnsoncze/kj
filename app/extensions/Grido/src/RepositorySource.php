<?php

namespace App\Extensions\Grido;

use Nette\Database\IRow;
use Nette\Utils\Callback;
use Ricaefeliz\Mappero\Entities\IEntity;
use App\Helpers\IteratorAggregates;
use Grido\DataSources\IDataSource;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class RepositorySource extends NObject implements IDataSource
{

    /** @var callable|null */
    protected $filterCallback;

    /** @var IRepositorySource */
    protected $repository;

    /** @var string method name for find data */
    protected $method = "findBy";

    /** @var string method name for get count of data */
    protected $methodCount = "count";

    /** @var null|callable */
    protected $modifyData;

    /** @var array */
    protected $filters = [
        "where" => []
    ];



    public function __construct(IRepositorySource $source)
    {
        $this->repository = $source;
    }



    /**
     * @param $callback callable
     * @return self
    */
    public function setFilterCallback(callable $callback) : self
    {
        $this->filterCallback = $callback;
        return $this;
    }



    /**
     * @param $method string
     * @return self
     */
    public function setRepositoryMethod($method)
    {
        $this->method = $method;
        return $this;
    }



    /**
     * @param string $methodCount
     * @return self
     */
    public function setMethodCount($methodCount)
    {
        $this->methodCount = $methodCount;
        return $this;
    }



    /**
     * @param array $condition
     * @return void
     */
    public function filter(array $condition)
    {
        foreach ($condition as $c) {
            $this->filters["where"][] = $this->filterCallback ? call_user_func($this->filterCallback, $c) : $c;
        }
    }



    /**
     * @param int $offset
     * @param int $limit
     * @return void
     */
    public function limit($offset, $limit)
    {
        $this->filters["offset"] = $offset;
        $this->filters["limit"] = $limit;
    }



    /**
     * @param array $sorting
     * @return void
     */
    public function sort(array $sorting)
    {
        foreach ($sorting as $column => $sort) {
            $this->filters["sort"] = [$column, $sort];
        }
    }



    /**
     * @param $column string
     * @param $sort string
     * @return self
     */
    public function setDefaultSort($column, $sort)
    {
        $this->filters["sort"] = [$column, $sort];
        return $this;
    }



    /**
     * @param callable $callback
     * @return RepositorySource
     */
    public function setModifyData(callable $callback) : self
    {
        $this->modifyData = $callback;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getModifyData()
    {
        return $this->modifyData;
    }



    /**
     * @param $pagination bool if return data for pagination
     * @return array
     */
    public function getFilters($pagination = true)
    {
        $filters["where"] = $this->filters["where"];
        $filters["sort"] = isset($this->filters["sort"]) ? $this->filters["sort"] : null;
        if ($pagination) {
            $filters["limit"] = isset($this->filters["limit"]) ? $this->filters["limit"] : null;
            $filters["offset"] = isset($this->filters["offset"]) ? $this->filters["offset"] : null;
        }
        return $filters;
    }



    /**
     * @param mixed $column
     * @param array $conditions
     * @param int $limit
     * @return array
     */
    public function suggest($column, array $conditions, $limit)
    {
        throw new \Exception("Not implement yet");
    }



    /**
     * @return int
     */
    public function getCount()
    {
        $result = $this->repository->{$this->methodCount}($this->getFilters(false));
        return $result->getCount();
    }



    /**
     * @return array|IEntity[]
     * @throws RepositorySourceException
     */
    public function getData()
    {
        $result = $this->repository->{$this->method}($this->getFilters());
        $data = [];
        if ($result) {
            $dataArray = is_array($result) ? $result : [$result];
            if (end($dataArray) instanceof IRow){
                $data = $dataArray;
            } elseif (end($dataArray) instanceof \IteratorAggregate) {
                $data = IteratorAggregates::toArray($result);
            } elseif (end($dataArray) instanceof IEntity) {
                $data = $result;
            } else {
                throw new RepositorySourceException("Unknown type of result.");
            }
        }
        //modify data
        if (is_callable($this->getModifyData())) {
            return Callback::invoke($this->getModifyData(), $this, $data);
        }
        return $data;
    }


}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class RepositorySourceException extends \Exception
{


}