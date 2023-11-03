<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\Block\BannerSlim;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Item
{


    /** @var string|null */
    protected $bannerClass;

    /** @var string|null */
    protected $backgroundColor;

    /** @var string|null */
    protected $title;

    /** @var string|null */
    protected $description;

    /** @var string|null */
    protected $footText;

    /** @var string|null */
    protected $linkCallUrl;

    /** @var string|null */
    protected $linkCallAnchor;

    /** @var string|null */
    protected $linkEmailUrl;

    /** @var string|null */
    protected $linkEmailAnchor;

    /** @var bool */
    protected $revert = FALSE;



    /**
     * @return null|string
     */
    public function getBannerClass()
    {
        return $this->bannerClass;
    }



    /**
     * @param string $bannerClass
     */
    public function setBannerClass(string $bannerClass)
    {
        $this->bannerClass = $bannerClass;
    }



    /**
     * @return null|string
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }



    /**
     * @param string $backgroundColor
     */
    public function setBackgroundColor(string $backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
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
    public function getFootText()
    {
        return $this->footText;
    }



    /**
     * @param string $footText
     */
    public function setFootText(string $footText)
    {
        $this->footText = $footText;
    }



    /**
     * @return null|string
     */
    public function getLinkCallUrl()
    {
        return $this->linkCallUrl;
    }



    /**
     * @param string $linkCallUrl
     */
    public function setLinkCallUrl(string $linkCallUrl)
    {
        $this->linkCallUrl = $linkCallUrl;
    }



    /**
     * @return null|string
     */
    public function getLinkCallAnchor()
    {
        return $this->linkCallAnchor;
    }



    /**
     * @param string $linkCallAnchor
     */
    public function setLinkCallAnchor(string $linkCallAnchor)
    {
        $this->linkCallAnchor = $linkCallAnchor;
    }



    /**
     * @return null|string
     */
    public function getLinkEmailUrl()
    {
        return $this->linkEmailUrl;
    }



    /**
     * @param string $linkEmailUrl
     */
    public function setLinkEmailUrl(string $linkEmailUrl)
    {
        $this->linkEmailUrl = $linkEmailUrl;
    }



    /**
     * @return null|string
     */
    public function getLinkEmailAnchor()
    {
        return $this->linkEmailAnchor;
    }



    /**
     * @param string $linkEmailAnchor
     */
    public function setLinkEmailAnchor(string $linkEmailAnchor)
    {
        $this->linkEmailAnchor = $linkEmailAnchor;
    }



    /**
     * @return bool
     */
    public function isRevert(): bool
    {
        return $this->revert;
    }



    /**
     * @param bool $revert
     */
    public function setRevert(bool $revert)
    {
        $this->revert = $revert;
    }


}