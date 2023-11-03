<?php

declare(strict_types = 1);

namespace App\Components\ProductParameterGroupList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\ProductParameterGroup\ProductParameterGroupRepositoryFactory;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepositoryFactory;
use Grido\Grid;
use Nette\Utils\Html;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupList extends GridoComponent
{


    /** @var ProductParameterGroupRepositoryFactory */
    protected $productParameterGroupTranslationRepositoryFactory;



    public function __construct(GridoFactory $gridoFactory,
                                ProductParameterGroupTranslationRepositoryFactory $productParameterGroupTranslationRepositoryFactory)
    {
        parent::__construct($gridoFactory);

        $this->productParameterGroupTranslationRepositoryFactory = $productParameterGroupTranslationRepositoryFactory;
    }



    /**
     * @return \Grido\Grid
     */
    public function createComponentList() : Grid
    {
        //Get default locale
        $localeResolver = new LocalizationResolver();
        $default = $localeResolver->getDefault();

        //Repo and model
        $repo = $this->productParameterGroupTranslationRepositoryFactory->create();
        $model = new RepositorySource($repo);
        $model->filter([["languageId", "=", $default->getId()]]);
        $model->setDefaultSort('name', 'ASC');

        //Create grido
        $grido = $this->gridoFactory->create();
        $grido->setModel($model);
        $grido->addColumnText("name", "Název")
            ->setSortable()
            ->setFilterText();

        //Styles
        $grido->getColumn("name")->getHeaderPrototype()->style["width"] = "40%";

        //Actions
        $grido->addActionHref("edit", "", "ProductParameterGroup:edit")
            ->setCustomRender(function ($row) {
                $html = Html::el("a");
                $html->setAttribute("class", "btn btn-default btn-xs btn-mini")
                    ->setAttribute("href", $this->presenter->link("ProductParameterGroup:edit", ["id" => $row["productParameterGroupId"]]))
                    ->addHtml('<i class="fa fa-pencil"></i>');
                return $html;
            });
        $grido->addActionHref("remove", "", "remove!")
            ->setCustomRender(function ($row) {
                $html = Html::el("a");
                $html->setAttribute("class", "btn btn-default btn-xs btn-mini")
                    ->setAttribute("href", $this->presenter->link("remove!", ["id" => $row["productParameterGroupId"]]))
                    ->setAttribute("data-grido-confirm", sprintf("Opravdu si přejete smazat skupinu parametrů '%s'? Přijdete tím o všechna související nastavení.", $row["name"]))
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