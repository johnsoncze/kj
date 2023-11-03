<?php

namespace App;

use Ricaefeliz\Mappero\Exceptions\EntityException;
use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @deprecated use StateTrait
 */
trait StatusTrait
{


    /**
     * @param $status string
     * @throws EntityException
     */
    public function setStatus(string $status)
    {
        Entities::hasProperty($this, 'status');
        self::checkStatuses();
        $statusKeys = array_keys(self::$statuses);
        if (!in_array($status, $statusKeys)) {
            throw new EntityException("Unknown status '{$status}'. Available statuses are '" . implode(",", $statusKeys) . "'.");
        }
        $this->status = $status;
    }



    /**
     * @return string|null
     */
    public function getStatus()
    {
        Entities::hasProperty($this, 'status');
        self::checkStatuses();
        return $this->status;
    }



    /**
     * @return array
     */
    public static function getStatuses()
    {
        self::checkStatuses();
        return self::$statuses;
    }



    /**
     * @throws EntityException
     */
    protected static function checkStatuses()
    {
        if (!isset(self::$statuses) || !is_array(self::$statuses)) {
            throw new EntityException("Missing array with statuses. You must create an array with basic structure [status_key => [status_key =>.., translate =>..], status_key=>.. ].");
        }
    }
}