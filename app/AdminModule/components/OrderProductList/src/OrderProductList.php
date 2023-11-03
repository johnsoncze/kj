<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\OrderProductList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Helpers\Prices;
use App\Order\Order;
use App\Order\Product\Product;
use Grido\DataSources\ArraySource;
use Grido\Grid;
use Kdyby\Translation\ITranslator;
use Nette\Utils\Html;
use App\Libs\FileManager\FileManager;
use App\Product\Photo\PhotoTrait;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OrderProductList extends GridoComponent
{


    use PhotoTrait;

    /** @var ITranslator */
    private $translator;

    /** @var Order|null */
    private $order;

    /** @var FileManager */
    protected $fileManager;



    public function __construct(GridoFactory $gridoFactory,
                                ITranslator $translator,
                                  FileManager $fileManager)
    {
        parent::__construct($gridoFactory);
        $this->translator = $translator;
        $this->fileManager = $fileManager;
    }



    /**
     * @param $order Order
     * @return self
     */
    public function setOrder(Order $order) : self
    {
        $this->order = $order;
        return $this;
    }



    /**
     * @return Order
     * @throws \InvalidArgumentException missing order
     */
    public function getOrder() : Order
    {
        if (!$this->order instanceof Order) {
            throw new \InvalidArgumentException('Missing order object.');
        }
        return $this->order;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $source = new ArraySource($this->getOrder()->getProducts());

        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $code = $grid->addColumnText('code', 'Kód');
        $grid->addColumnText('photo', 'Fotografie')
            ->setCustomRender(function (Product $product) {
                return $this->getThumbnailToPhoto($product->getCatalogProduct(true), $this->fileManager, $this->getPresenter()->context);
            });
        $name = $grid->addColumnText('name', 'Název');
        $name->setCustomRender(function (Product $product) {
            return $product->getTranslatedName();
        });

        $quantity = $grid->addColumnText('quantity', 'Množství');
        $quantity->setSortable();

        $stock = $grid->addColumnText('inStock', 'Skladem v čase objednávky');
        $stock->setCustomRender(function (Product $product) {
        	if ($product->wasInStock() === FALSE) {
        		$text = 'Ne';
        		if ($product->getProductionTimeId()) {
        			$text .= sprintf('<br>Doba výroby: %s', $product->getTranslatedProductionTimeName());
        			$text .= sprintf('<br>Příplatek: %s', $product->getSurchargePercent() ? sprintf('%s Kč (+%s %%)', Prices::toUserFriendlyFormat($product->getSurcharge()), $product->getSurchargePercent()) : '-');
				}
				return $text;
			}
        	return 'Ano';
        });

        $discount = $grid->addColumnNumber('discount', 'Sleva');
        $discount->setCustomRender(function (Product $product) {
            return $product->getDiscount() ? number_format($product->getDiscount(), 0) . ' %' : '-';
        });

        $unitPrice = $grid->addColumnNumber('unitPrice', 'Cena za kus');
        $unitPrice->setNumberFormat(2, ',', ' ');
        $unitPrice->setSortable();

        $summaryPrice = $grid->addColumnNumber('summaryPrice', 'Cena celkem');
        $summaryPrice->setNumberFormat(2, ',', ' ');
        $summaryPrice->setSortable();

        //actions
        $grid->addActionHref('detail', '', 'Product:detail')
            ->setCustomRender(function (Product $product) {
                if ($product->getProductId()) {
                    $el = Html::el('a');
                    $el->setAttribute('class', 'btn btn-xs btn-default');
                    $el->setHtml('<i class="fa fa-eye"></i>');
                    $el->setAttribute('href', $this->getPresenter()->link('Product:edit', ['id' => $product->getProductId()]));
                    return $el;
                }
                return '<em>Produkt již není v nabídce</em>';
            });

        //styles
        $code->getHeaderPrototype()->style['width'] = '10%';
        $name->getHeaderPrototype()->style['width'] = '20%';
        $quantity->getHeaderPrototype()->style['width'] = '10%';
        $stock->getHeaderPrototype()->style['width'] = '15%';
        $unitPrice->getHeaderPrototype()->style['width'] = '15%';
        $discount->getHeaderPrototype()->style['width'] = '10%';

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
