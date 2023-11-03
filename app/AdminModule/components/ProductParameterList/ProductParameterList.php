<?php

declare(strict_types = 1);

namespace App\Components\ProductParameterList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\ProductParameter\ProductParameterTranslationEntity;
use App\ProductParameter\ProductParameterTranslationRepositoryFactory;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use Grido\Grid;
use Nette\Utils\Html;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterList extends GridoComponent
{



    /** @var ProductParameterTranslationRepositoryFactory */
    protected $productParameterTranslationRepoFactory;

    /** @var ProductParameterGroupEntity|null */
    protected $productParameterGroupEntity;



    public function __construct(GridoFactory $gridoFactory,
                                ProductParameterTranslationRepositoryFactory $productParameterTranslationRepositoryFactory)
    {
        parent::__construct($gridoFactory);

        $this->productParameterTranslationRepoFactory = $productParameterTranslationRepositoryFactory;
    }



    /**
     * @param ProductParameterGroupEntity $productParameterGroupEntity
     * @return ProductParameterList
     */
    public function setProductParameterGroupEntity(ProductParameterGroupEntity $productParameterGroupEntity)
    : self
    {
        $this->productParameterGroupEntity = $productParameterGroupEntity;
        return $this;
    }



    /**
     * @return ProductParameterGroupEntity
     * @throws ProductParameterListException
     */
    public function getProductParameterGroupEntity() : ProductParameterGroupEntity
    {
        if (!$this->productParameterGroupEntity instanceof ProductParameterGroupEntity) {
            throw new ProductParameterListException(sprintf("You must set object '%s'.", ProductParameterGroupEntity::class));
        }
        return $this->productParameterGroupEntity;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        //Get default locale
        $localeResolver = new LocalizationResolver();
        $default = $localeResolver->getDefault();

        //create model
        $model = new RepositorySource($this->productParameterTranslationRepoFactory->create());
        $model->setDefaultSort("value", "ASC");
        $model->filter([
            ["languageId", "=", $default->getId()],
            //only parameters of set group
            //todo entity column
            ["productParameterId", "IN.SQL", sprintf('(SELECT pp_id FROM product_parameter WHERE pp_product_parameter_group_id = \'%d\')', $this->getProductParameterGroupEntity()->getId())],
        ]);

        //create grido
        $grido = $this->gridoFactory->create();
        $grido->setModel($model);
        $grido->addColumnText("value", "Hodnota")
            ->setSortable()
            ->setFilterText();

        //styles
        $grido->getColumn("value")->getHeaderPrototype()->style["width"] = "30%";

        //actions
        $grido->addActionHref("edit", "", "ProductParameter:edit")
            ->setCustomRender(function(ProductParameterTranslationEntity $entity){
                $html = Html::el("a");
                $html->setAttribute("class", "btn btn-default btn-xs btn-mini")
                    ->setAttribute("href", $this->presenter->link("ProductParameter:edit", ["id" => $entity->getProductParameterId()]))
                    ->addHtml('<i class="fa fa-pencil"></i>');
                return $html;
            });
        $grido->addActionHref("remove", "", "remove!")
            ->setCustomRender(function (ProductParameterTranslationEntity $entity) {
                $html = Html::el("a");
                $html->setAttribute("class", "btn btn-default btn-xs btn-mini")
                    ->setAttribute("href", $this->presenter->link("remove!", ["id" => $entity->getProductParameterId()]))
                    ->setAttribute("data-grido-confirm", sprintf("Opravdu si přejete smazat skupinu parametrů '%s'? Přijdete tím o všechna související nastavení.", $entity->getValue()))
                    ->addHtml('<i class="fa fa-trash"></i>');
                return $html;
            });

        return $grido;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }
}