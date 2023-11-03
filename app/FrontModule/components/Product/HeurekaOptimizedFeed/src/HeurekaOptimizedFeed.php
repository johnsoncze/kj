<?php

declare(strict_types=1);

namespace App\FrontModule\Components\Product\HeurekaOptimizedFeed;

use App\FrontModule\Components\Feed\AbstractFeed\AbstractFeed;
use App\Product\Product;
use Nette\Database\Context as NetteDatabase;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class HeurekaOptimizedFeed extends AbstractFeed
{
    /** @var string tag for cache of feed */
    const CACHE_TAG = 'heurekaOptimizedFeed';

    /**
     * @var NetteDatabase
     */
    public NetteDatabase $ndb;

    /**
     * @param NetteDatabase $ndb
     */
    public function __construct(NetteDatabase $ndb)
    {
        parent::__construct();
        $this->ndb = $ndb;
        $this->templatePath = __DIR__ . '/heurekaOptimizedFeed.latte';
    }

    public function render()
    {
        $this->template->setFile($this->templatePath);
        $this->template->render();
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getProductsData(int $limit = 0): array
    {
        return $this->ndb->query("
            SELECT product.p_id, product.p_code, product_translation.pt_name, product.p_heureka_category, product_translation.pt_description,
                   product_translation.pt_url, product.p_stock > 0 AS is_in_stock, product.p_photo, product.p_price
            FROM product
            JOIN product_translation ON (product_translation.pt_product_id = product.p_id)
            
            WHERE product_translation.pt_language_id = 1
            AND product.p_state = ?
            AND product.p_sale_online = 1
            LIMIT ?
        ",
            Product::PUBLISH,
            $limit ?: PHP_INT_MAX
        )->fetchAll();
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getProductParametersList(int $productId): array
    {
        return $this->ndb->query("
            SELECT product_parameter_relationship.ppr_product_id, ppgt_filtration_title, GROUP_CONCAT(product_parameter_translation.ppt_value SEPARATOR ', ') AS param_values
            FROM product_parameter_relationship
            JOIN product_parameter ON (product_parameter_relationship.ppr_parameter_id = product_parameter.pp_id)
            JOIN product_parameter_translation ON (product_parameter_translation.ppt_language_id = 1 AND product_parameter_translation.ppt_product_parameter_id = product_parameter.pp_id)
            JOIN product_parameter_group_translation ON (product_parameter_group_translation.ppgt_language_id = 1 AND product_parameter_group_translation.ppgt_product_parameter_group_id = product_parameter.pp_product_parameter_group_id)                       
            WHERE product_parameter_relationship.ppr_product_id = ?
            GROUP BY product_parameter_relationship.ppr_product_id, ppgt_filtration_title
        ", $productId)->fetchAll();

    }

}