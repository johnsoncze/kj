<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductParameterSortForm;

use App\ProductParameter\ProductParameterSortFacadeException;
use App\ProductParameter\ProductParameterSortFacadeFactory;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterSortFormSuccess extends NObject
{


    /** @var Context */
    protected $database;

    /** @var ProductParameterSortFacadeFactory */
    protected $productParameterSortFacadeFactory;



    public function __construct(Context $context,
                                ProductParameterSortFacadeFactory $parameterSortFacadeFactory)
    {
        $this->database = $context;
        $this->productParameterSortFacadeFactory = $parameterSortFacadeFactory;
    }



    /**
     * @param ProductParameterSortForm $productParameterSortForm
     * @param Form $form
     * @param $data array
     */
    public function process(ProductParameterSortForm $productParameterSortForm,
                            Form $form,
                            array $data)
    {
        $presenter = $productParameterSortForm->getPresenter();
        $productParameterGroupEntity = $productParameterSortForm->getProductParameterGroupEntity();

        try {
            $this->database->beginTransaction();
            $sortFacade = $this->productParameterSortFacadeFactory->create();
            $sortFacade->saveSort($productParameterGroupEntity, $data);
            $this->database->commit();

            $presenter->flashMessage('Řazení bylo uloženo.', 'success');
            $presenter->redirect('this');
        } catch (ProductParameterSortFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }
}