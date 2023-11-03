<?php

namespace Ricaefeliz\Mappero\ResultObjects;

use App\NObject;


/**
 * @method getCount()
 */
class CountDTO extends NObject
{


    /** @var int */
    protected $count;



    public function __construct($count)
    {
        $this->count = $count;
    }
}