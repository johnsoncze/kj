<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\SiteMap;

use App\Language\LanguageEntity;
use App\Product\Product;
use App\Product\ProductPublishedRepository;
use Nette\Application\UI\Control;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SiteMap extends Control
{


    /** @var string */
    const DEFAULT_TEMPLATE = __DIR__ . '/default.latte';

    /** @var LanguageEntity|null */
    private $language;

    /** @var ProductPublishedRepository */
    private $productRepo;



    public function __construct(ProductPublishedRepository $productPublishedRepo)
    {
        parent::__construct();
        $this->productRepo = $productPublishedRepo;
    }



    /**
     * @param $language LanguageEntity
     * @return self
     */
    public function setLanguage(LanguageEntity $language) : self
    {
        $this->language = $language;
        return $this;
    }



    /**
     * @param $limit int
     * @param $offset int|null
     * @return array|Product[]
     */
    public function getProducts(int $limit, int $offset = NULL) : array
    {
        $products = $this->productRepo->findByLimit($limit, $offset);
        return $products ?: [];
    }



    /**
     * @return CountDTO
     */
    public function getProductsCount() : CountDTO
    {
        $productRepo = $this->productRepo;
        return $productRepo->getCount();
    }



    public function render()
    {
        $this->template->language = $this->language;
        $this->template->setFile(self::DEFAULT_TEMPLATE);
        $this->template->render();
    }



    public function renderToString()
    {
        $this->template->language = $this->language;
        $this->template->setFile(self::DEFAULT_TEMPLATE);
        return (string)$this->template;
    }



    /**
     * @return string
     */
    public function getCacheId() : string
    {
        return 'productSitemap' . $this->language->getId();
    }
}