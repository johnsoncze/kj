<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Catalog\SortForm;

use App\Catalog\CatalogFacadeException;
use App\Catalog\CatalogFacadeFactory;
use App\Catalog\CatalogRepository;
use App\Components\SortForm\SortFormFactory;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SortForm extends Control
{


    /** @var CatalogFacadeFactory */
    private $catalogFacadeFactory;

    /** @var CatalogRepository */
    private $catalogRepo;

    /** @var Context */
    private $database;

    /** @var SortFormFactory */
    private $sortFormFactory;

    /** @var string|null */
    private $type;



    public function __construct(CatalogFacadeFactory $catalogFacadeFactory,
                                CatalogRepository $catalogRepo,
                                Context $database,
                                SortFormFactory $sortFormFactory)
    {
        parent::__construct();
        $this->catalogFacadeFactory = $catalogFacadeFactory;
        $this->catalogRepo = $catalogRepo;
        $this->database = $database;
        $this->sortFormFactory = $sortFormFactory;
    }



    /**
     * @param $type string
     * @return self
     */
    public function setType(string $type) : self
    {
        $this->type = $type;
        return $this;
    }



    /**
     * @return \App\Components\SortForm\SortForm
     */
    public function createComponentSortForm() : \App\Components\SortForm\SortForm
    {
        $sortForm = $this->sortFormFactory->create();
        $sortForm->setItems($this->getItems());
        $sortForm->setOnSuccess([$this, 'formSuccess']);
        return $sortForm;
    }



    /**
     * @param $form Form
     * @param $sorting array
     * @throws AbortException
     */
    public function formSuccess(Form $form, array $sorting)
    {
        $sorting = array_flip($sorting);
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $catalogFacade = $this->catalogFacadeFactory->create();
            $catalogFacade->saveSorting($sorting);
            $this->database->commit();

            $presenter->flashMessage('Řazení bylo uloženo.', 'success');
            $presenter->redirect('this');
        } catch (CatalogFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @return array
     */
    private function getItems() : array
    {
        $response = [];
        $items = $this->catalogRepo->findByType($this->type);
        foreach ($items as $item) {
            $response[$item->getId()] = $item->getTranslation()->getTitle();
        }
        return $response;
    }
}