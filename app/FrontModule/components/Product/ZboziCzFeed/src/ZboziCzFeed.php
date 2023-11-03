<?php

declare(strict_types=1);

namespace App\FrontModule\Components\Product\ZboziCzFeed;

use App\FrontModule\Components\Product\AbstractProductFeed\AbstractProductFeed;
use App\Product\Product;
use App\Product\ProductDTOFactory;
use App\Product\ProductPublishedRepositoryFactory;
use App\Product\ProductRepositoryFactory;
use Nette\Application\UI\Control;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ZboziCzFeed extends AbstractProductFeed
{


    /** @var string tag for cache of feed */
    const CACHE_TAG = 'zboziCzFeed';


    public function __construct(ProductPublishedRepositoryFactory $productRepositoryFactory,
                                ProductDTOFactory $productDTOFactory)
    {
        parent::__construct($productRepositoryFactory, $productDTOFactory);
        $this->templatePath = __DIR__ . '/default.latte';
    }

}