<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\Block\Timeline;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Item
{


    /** @var bool */
    protected $reverse = FALSE;

    /** @var string|null */
    protected $contentClass;

    /** @var string|null */
    protected $title;

    /** @var string|null */
    protected $titleClass;

    /** @var string|null */
    protected $subtitle;

    /** @var string|null */
    protected $subtitleClass;

    /** @var string|null */
    protected $description;

    /** @var string|null */
    protected $descriptionClass;

    /** @var string|null */
    protected $images;

    /** @var string|null */
    protected $imagesClass;

    /** @var string|null */
    protected $rowClass;

    /** @var string|null */
    protected $item1Class;

    /** @var string|null */
    protected $item2Class;

    /** @var string|null */
    protected $html;



    /**
     * @return bool
     */
    public function isReverse(): bool
    {
        return $this->reverse;
    }



    /**
     * @param bool $reverse
     */
    public function setReverse(bool $reverse)
    {
        $this->reverse = $reverse;
    }



    /**
     * @return string|null
     */
    public function getContentClass()
    {
        return $this->contentClass;
    }



    /**
     * @param string $contentClass
     */
    public function setContentClass(string $contentClass)
    {
        $this->contentClass = $contentClass;
    }



    /**
     * @return null|string
     */
    public function getTitle()
    {
        return $this->title;
    }



    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }



    /**
     * @return null|string
     */
    public function getTitleClass()
    {
        return $this->titleClass;
    }



    /**
     * @param string $titleClass
     */
    public function setTitleClass(string $titleClass)
    {
        $this->titleClass = $titleClass;
    }



    /**
     * @return null|string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }



    /**
     * @param string $subtitle
     */
    public function setSubtitle(string $subtitle)
    {
        $this->subtitle = $subtitle;
    }



    /**
     * @return string
     */
    public function getSubtitleClass()
    {
        return $this->subtitleClass;
    }



    /**
     * @param string $subtitleClass
     */
    public function setSubtitleClass(string $subtitleClass)
    {
        $this->subtitleClass = $subtitleClass;
    }



    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }



    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }



    /**
     * @return null|string
     */
    public function getDescriptionClass()
    {
        return $this->descriptionClass;
    }



    /**
     * @param string $descriptionClass
     */
    public function setDescriptionClass(string $descriptionClass)
    {
        $this->descriptionClass = $descriptionClass;
    }



    /**
     * @return null|string
     */
    public function getImages()
    {
        return $this->images;
    }



    /**
     * @param string $images
     */
    public function setImages(string $images)
    {
        $this->images = $images;
    }



    /**
     * @return null|string
     */
    public function getImagesClass()
    {
        return $this->imagesClass;
    }



    /**
     * @param string $imagesClass
     */
    public function setImagesClass(string $imagesClass)
    {
        $this->imagesClass = $imagesClass;
    }



    /**
     * @return string|null
     */
    public function getRowClass()
    {
        return $this->rowClass;
    }



    /**
     * @param string $rowClass
     */
    public function setRowClass(string $rowClass)
    {
        $this->rowClass = $rowClass;
    }



    /**
     * @return string|null
     */
    public function getItem1Class()
    {
        return $this->item1Class;
    }



    /**
     * @param string $item1Class
     */
    public function setItem1Class(string $item1Class)
    {
        $this->item1Class = $item1Class;
    }



    /**
     * @return null|string
     */
    public function getItem2Class()
    {
        return $this->item2Class;
    }



    /**
     * @param string $item2Class
     */
    public function setItem2Class(string $item2Class)
    {
        $this->item2Class = $item2Class;
    }



    /**
     * @return null|string
     */
    public function getHtml()
    {
        return $this->html;
    }



    /**
     * @param string $html
     */
    public function setHtml(string $html)
    {
        $this->html = $html;
    }


}