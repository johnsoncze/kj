<?php

namespace App;

use Kdyby\Translation\Translator;
use App\NObject;


abstract class BaseService extends NObject
{


    /** @var Translator */
    protected $translator;



    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
}