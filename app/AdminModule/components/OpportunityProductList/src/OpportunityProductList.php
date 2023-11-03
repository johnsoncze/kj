<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\OpportunityProductList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Entities;
use App\Helpers\Prices;
use App\Libs\FileManager\FileManager;
use App\Opportunity\Opportunity;
use App\Opportunity\Product\Product;
use App\Opportunity\Product\ProductRepository;
use App\Product\Photo\PhotoTrait;
use App\Product\Production\ProductionTrait;
use App\Product\ProductRepository AS CatalogProductRepository;
use Grido\Grid;
use Kdyby\Translation\ITranslator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OpportunityProductList extends GridoComponent
{


    use PhotoTrait;

    /** @var CatalogProductRepository */
    private $catalogProductRepo;

    /** @var FileManager */
    private $fileManager;

    /** @var Opportunity|null */
    private $opportunity;

    /** @var ProductRepository */
    private $productRepo;

    /** @var ITranslator */
    private $translator;



    public function __construct(CatalogProductRepository $catalogProductRepo,
                                FileManager $fileManager,
                                GridoFactory $gridoFactory,
                                ITranslator $translator,
                                ProductRepository $productRepository)
    {
        parent::__construct($gridoFactory);
        $this->catalogProductRepo = $catalogProductRepo;
        $this->fileManager = $fileManager;
        $this->productRepo = $productRepository;
        $this->translator = $translator;
    }



    /**
     * @return Opportunity
     * @throws \InvalidArgumentException missing opportunity
     */
    public function getOpportunity() : Opportunity
    {
        if ($this->opportunity === NULL) {
            throw new \InvalidArgumentException(sprintf('Missing \'%s\' object.', Opportunity::class));
        }
        return $this->opportunity;
    }



    /**
     * @param $opportunity Opportunity
     * @return self
     */
    public function setOpportunity(Opportunity $opportunity) : self
    {
        $this->opportunity = $opportunity;
        return $this;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $catalogProducts = new ArrayHash();

        $source = new RepositorySource($this->productRepo);
        $source->setDefaultSort('code', 'ASC');
        $source->filter([['opportunityId', '=', $this->getOpportunity()->getId()]]);
        $source->setModifyData(function ($source, $data) use ($catalogProducts) {
            if ($data) {
                $productId = Entities::getProperty($data, 'productId');
                $products = $this->catalogProductRepo->findByMoreId($productId);
                foreach ($products as $key => $product) {
                    $catalogProducts[$key] = $product;
                }
            }
            return $data;
        });

        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $grid->addColumnText('code', 'Kód');
        $grid->addColumnText('photo', 'Fotografie')
            ->setCustomRender(function ($product) use ($catalogProducts) {
                $productObject = $catalogProducts[$product->getProductId()] ?? new \App\Product\Product(); //workaround for show image placeholder if the product does not exist
                return $this->getThumbnailToPhoto($productObject, $this->fileManager, $this->getPresenter()->context);
            });
        $grid->addColumnText('name', 'Produkt')
            ->setCustomRender(function(Product $product) {
                $comment = $product->getComment();
                $text = $product->getName();
                if($comment) {
                    $text .= '<br><b>Poznámka:</b> ' . $comment;
                }
                return $text;
            });
        $grid->addColumnText('quantity', 'Množství');
        $grid->addColumnText('productionTime', 'Doba výroby')
            ->setCustomRender(function (Product $row){
                $productionTime = $row->getProductionTime();
                if ($productionTime) {
                    $productionTimeObject = ProductionTrait::getProductionTimes()[$row->getProductionTime()];
                    return $this->translator->translate($productionTimeObject->getTranslationKey());
                }
                return '-';
            });
        $grid->addColumnNumber('productionTimePercent', 'Procento příplatku za dobu zhotovení')
            ->setReplacement([NULL => '-'])
            ->setCustomRender(function (Product $product){
                $percent = $product->getProductionTimePercent();
                return $percent ? Prices::toUserFriendlyFormat($percent) . ' %' : '-';
            });
        $discount = $grid->addColumnNumber('discount', 'Sleva');
        $discount->setCustomRender(function (Product $product) {
            return $product->getDiscount() ? number_format($product->getDiscount(), 0) . ' %' : '-';
        });
        $grid->addColumnNumber('price', 'Cena')
            ->setCustomRender(function (Product $row) {
                return Prices::toUserFriendlyFormat($row->getSummaryPrice()) . ' Kč';
            });

        //actions
        $link = 'Product:edit';
        $grid->addActionHref('edit', '', $link)
            ->setCustomRender(function (Product $product) use ($catalogProducts, $link) {
                if (isset($catalogProducts[$product->getProductId()])) {
                    $el = Html::el('a');
                    $el->setAttribute('href', $this->presenter->link($link, ['id' => $product->getProductId()]));
                    $el->setAttribute('class', 'grid-action-detail btn btn-default btn-xs btn-mini');
                    $el->setHtml('<i class="fa fa-eye"></i>');
                    return $el;
                }
                return 'Produkt již není v nabídce.';
            });

        //styles
        $grid->getColumn('code')->getHeaderPrototype()->style['width'] = '15%';
        $grid->getColumn('name')->getHeaderPrototype()->style['width'] = '25%';
        $grid->getColumn('quantity')->getHeaderPrototype()->style['width'] = '5%';
        $grid->getColumn('productionTime')->getHeaderPrototype()->style['width'] = '20%';
        $grid->getColumn('productionTimePercent')->getHeaderPrototype()->style['width'] = '10%';
        $grid->getColumn('discount')->getHeaderPrototype()->style['width'] = '10%';
        $grid->getColumn('price')->getHeaderPrototype()->style['width'] = '10%';

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}