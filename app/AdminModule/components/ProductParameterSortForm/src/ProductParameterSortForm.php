<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductParameterSortForm;

use App\Components\SortForm\SortForm;
use App\Components\SortForm\SortFormFactory;
use App\Helpers\Entities;
use App\ProductParameter\ProductParameterRepositoryFactory;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterSortForm extends \Nette\Application\UI\Control
{


    /** @var ProductParameterRepositoryFactory */
    protected $productParameterRepositoryFactory;

    /** @var SortFormFactory */
    protected $sortFormFactory;

    /** @var ProductParameterSortFormSuccessFactory */
    protected $productParameterSortFomSuccessFactory;

    /** @var ProductParameterGroupEntity|null */
    protected $productParameterGroupEntity;



    /**
     * ProductParameterSortForm constructor.
     * @param ProductParameterRepositoryFactory $productParameterRepositoryFactory
     * @param SortFormFactory $sortFormFactory
     * @param $productParameterSortFomSuccessFactory ProductParameterSortFormSuccessFactory
     */
    public function __construct(ProductParameterRepositoryFactory $productParameterRepositoryFactory,
                                SortFormFactory $sortFormFactory,
                                ProductParameterSortFormSuccessFactory $productParameterSortFomSuccessFactory)
    {
        $this->productParameterRepositoryFactory = $productParameterRepositoryFactory;
        $this->sortFormFactory = $sortFormFactory;
        $this->productParameterSortFomSuccessFactory = $productParameterSortFomSuccessFactory;
    }



    /**
     * @param $productParameterGroupEntity ProductParameterGroupEntity
     * @return self
     */
    public function setProductParameterGroupEntity(ProductParameterGroupEntity $productParameterGroupEntity)
    : self
    {
        $this->productParameterGroupEntity = $productParameterGroupEntity;
        return $this;
    }



    /**
     * @return ProductParameterGroupEntity
     * @throws ProductParameterSortFormException
     */
    public function getProductParameterGroupEntity() : ProductParameterGroupEntity
    {
        if (!$this->productParameterGroupEntity instanceof ProductParameterGroupEntity) {
            throw new ProductParameterSortFormException(sprintf('You must set object \'%s\'.', ProductParameterGroupEntity::class));
        }
        return $this->productParameterGroupEntity;
    }



    /**
     * @return SortForm
     */
    public function createComponentForm() : SortForm
    {
        $sortForm = $this->sortFormFactory->create();
        $sortForm->setItems($this->getProductParameterList());
        $sortForm->setOnSuccess(function (Form $form, array $data) {
            $formSuccess = $this->productParameterSortFomSuccessFactory->create();
            $formSuccess->process($this, $form, $data);
        });

        return $sortForm;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }



    /**
     * @return array
     */
    protected function getProductParameterList() : array
    {
        $parameterGroup = $this->getProductParameterGroupEntity();
        $productParameterRepo = $this->productParameterRepositoryFactory->create();
        $parameters = $productParameterRepo->findByProductParameterGroupId($parameterGroup->getId());

        return $parameters ? Entities::toPair($parameters, 'id', 'value', Entities::VALUE_TRANSLATION_FLAG) : [];
    }
}