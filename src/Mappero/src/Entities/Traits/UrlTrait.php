<?php

namespace Ricaefeliz\Mappero\Entities\Traits;

use Ricaefeliz\Mappero\Annotations\PropertyException;
use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait UrlTrait
{


    /**
     * @param $url
     * @return self
     * @throws PropertyException
     */
    public function setUrl($url)
    {
        Entities::hasProperty($this, 'url');
        $this->url = $url;
        return $this;
    }



    /**
     * @return mixed
     * @throws PropertyException
     */
    public function getUrl()
    {
        Entities::hasProperty($this, 'url');
        return $this->url;
    }


}