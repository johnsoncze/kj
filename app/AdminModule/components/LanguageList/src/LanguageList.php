<?php

namespace App\Components\LanguageList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\FacadeException;
use App\Language\LanguageFacadeFactory;
use App\Language\LanguageRepositoryFactory;
use Nette\Database\Context;
use Nette\Utils\Html;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class LanguageList extends GridoComponent
{


    /** @var Context */
    protected $database;

    /** @var LanguageFacadeFactory */
    protected $languageFacadeFactory;



    public function __construct(GridoFactory $gridoFactory,
                                LanguageRepositoryFactory $languageRepositoryFactory,
                                Context $context,
                                LanguageFacadeFactory $languageFacadeFactory)
    {
        parent::__construct($gridoFactory);
        $this->repositoryFactory = $languageRepositoryFactory;
        $this->database = $context;
        $this->languageFacadeFactory = $languageFacadeFactory;
    }



    public function createComponentLanguageList()
    {
        $grido = $this->gridoFactory->create();
        $grido->setModel(new RepositorySource($this->repositoryFactory->create()));
        $grido->addColumnText("name", "Název")
            ->setSortable()
            ->setFilterText();
        $grido->addColumnText("prefix", "Prefix")
            ->setSortable()
            ->setFilterText();
        $grido->addColumnText("default", "Výchozí")
            ->setCustomRender(function ($row) {
                if ($row["default"]) {
                    return '<i class="fa fa-check" aria-hidden="true"></i>';
                }
                return '-';
            });
        $grido->addColumnText("active", "Aktivní")
            ->setCustomRender(function ($row) {
                if ($row["active"]) {
                    return '<i class="fa fa-check" aria-hidden="true"></i>';
                }
                return '-';
            });
        $grido->addActionHref("active", "")
            ->setCustomRender(function ($row) {
                if (!$row["default"]) {
                    return Html::el("a")
                        ->setHref($this->link(($row["active"] ? "deactive!" : "active!"), ["id" => $row["id"]]))
                        ->setText($row["active"] ? "deaktivovat" : "aktivovat");
                }
                return null;
            });
        $grido->getColumn("name")->getHeaderPrototype()->style["width"] = "20%";
        $grido->getColumn("prefix")->getHeaderPrototype()->style["width"] = "20%";
        $grido->getColumn("default")->getHeaderPrototype()->style["width"] = "10%";
        $grido->getColumn("active")->getHeaderPrototype()->style["width"] = "10%";
        return $grido;
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }



    /**
     * @param $id int
     * @return void
     */
    public function handleActive($id)
    {
        try {
            $this->database->beginTransaction();
            $language = $this->languageFacadeFactory->create()->active($id);
            $this->database->commit();
            $this->presenter->flashMessage("Jazyk {$language->getName()} byl aktivován.", "success");
        } catch (FacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        }
        $this->presenter->redirect("this");
    }



    /**
     * @param $id int
     * @return void
     */
    public function handleDeactive($id)
    {
        try {
            $this->database->beginTransaction();
            $language = $this->languageFacadeFactory->create()->deactive($id);
            $this->database->commit();
            $this->presenter->flashMessage("Jazyk {$language->getName()} byl deaktivován.", "success");
        } catch (FacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        }
        $this->presenter->redirect("this");
    }
}