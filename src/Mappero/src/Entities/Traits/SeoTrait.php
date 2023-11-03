<?php

namespace App;

use Ricaefeliz\Mappero\Helpers\Entities;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait SeoTrait
{


    /**
     * @param bool $index
     */
    public function setIndexSeo($index)
    {
        Entities::hasProperty($this, 'indexSeo');
        $this->indexSeo = $index;
    }



    /**
     * @return mixed
     */
    public function getIndexSeo()
    {
        Entities::hasProperty($this, 'indexSeo');
        return $this->indexSeo;
    }



    /**
     * @param bool $arg
     */
    public function setSiteMap($arg)
    {
        Entities::hasProperty($this, 'siteMap');
        $this->siteMap = $arg;
    }



    /**
     * @return mixed
     */
    public function getSiteMap()
    {
        Entities::hasProperty($this, 'siteMap');
        return $this->siteMap;
    }



    /**
     * @param bool $follow
     */
    public function setFollowSeo($follow)
    {
        Entities::hasProperty($this, 'followSeo');
        $this->followSeo = $follow;
    }



    /**
     * @return mixed
     */
    public function getFollowSeo()
    {
        Entities::hasProperty($this, 'followSeo');
        return $this->followSeo;
    }



    /**
     * @param $title string|null
     */
    public function setTitleSeo($title)
    {
        Entities::hasProperty($this, 'titleSeo');
        $this->titleSeo = $title;
    }



    /**
     * @return string|null
     */
    public function getTitleSeo()
    {
        Entities::hasProperty($this, 'titleSeo');
        return $this->titleSeo;
    }



    /**
     * @param $description string|null
     */
    public function setDescriptionSeo($description)
    {
        Entities::hasProperty($this, 'descriptionSeo');
        $this->descriptionSeo = $description;
    }



    /**
     * @return string|null
     */
    public function getDescriptionSeo()
    {
        Entities::hasProperty($this, 'descriptionSeo');
        return $this->descriptionSeo;
    }



    /**
     * @return bool
    */
    public function isIndexedInSeo() : bool
    {
        return (bool)$this->getIndexSeo();
    }



    /**
     * @return bool
    */
    public function isFollowedInSeo() : bool
    {
        return (bool)$this->getFollowSeo();
    }
}