<?php

declare(strict_types = 1);

namespace App\Components\ProductParameterForm;

use App\Components\TranslationFormTrait;
use App\Components\UrlFormContainer\UrlFormContainer;
use App\Components\UrlFormContainer\UrlFormContainerFactory;
use App\Helpers\Entities;
use App\ProductParameter\Helper\HelperRepository;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterForm extends Control
{


    /** @var string name of submit for save and add a new */
    const SUBMIT_ADD_NEW = "submitAddNew";

    use TranslationFormTrait;

    /** @var Context */
    protected $database;

    /** @var HelperRepository */
    protected $helperRepo;

    /** @var UrlFormContainerFactory */
    protected $urlFormFactory;

    /** @var ProductParameterFormSuccessFactory */
    protected $productParameterFormSuccessFactory;

    /** @var ProductParameterGroupEntity|null */
    protected $productParameterGroupEntity;

    /** @var ProductParameterEntity|null */
    protected $productParameterEntity;



    public function __construct(Context $context,
								HelperRepository $helperRepository,
                                UrlFormContainerFactory $urlFormContainerFactory,
                                ProductParameterFormSuccessFactory $productParameterFormSuccessFactory)
    {
        parent::__construct();
        $this->database = $context;
        $this->helperRepo = $helperRepository;
        $this->urlFormFactory = $urlFormContainerFactory;
        $this->productParameterFormSuccessFactory = $productParameterFormSuccessFactory;
    }



    /**
     * @param ProductParameterGroupEntity $productParameterGroupEntity
     * @return self
     */
    public function setProductParameterGroupEntity(ProductParameterGroupEntity $productParameterGroupEntity) : self
    {
        $this->productParameterGroupEntity = $productParameterGroupEntity;
        return $this;
    }



    /**
     * @param $need bool
     * @return ProductParameterGroupEntity|null
     * @throws ProductParameterFormException
     */
    public function getProductParameterGroupEntity(bool $need = TRUE)
    {
        if ($need === TRUE
            && !$this->productParameterGroupEntity instanceof ProductParameterGroupEntity
        ) {
            throw new ProductParameterFormException(sprintf('You must set \'%s\' object.', ProductParameterGroupEntity::class));
        }
        return $this->productParameterGroupEntity;
    }



    /**
     * @param ProductParameterEntity|NULL $productParameterEntity
     * @return self
     */
    public function setProductParameterEntity(ProductParameterEntity $productParameterEntity = NULL) : self
    {
        $this->productParameterEntity = $productParameterEntity;
        return $this;
    }



    /**
     * @return ProductParameterEntity|null
     */
    public function getProductParameterEntity()
    {
        return $this->productParameterEntity;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
    	$helperList = $this->getHelperList($this->productParameterGroupEntity);

        $form = new Form();
        $form->addText("value", "Název*")
            ->setAttribute("placeholder", "Např.: Zlato")
            ->setAttribute("class", "form-control")
            ->setRequired("Zadejte název parametru.")
            ->setAttribute('autofocus')
            ->setMaxLength(100);
        $form->addComponent($this->urlFormFactory->create(), UrlFormContainer::NAME);
        $form->addSelect('helperId', 'Hodnota', $helperList)
			->setPrompt('- vyberte -')
			->setAttribute('class', 'form-control');
        $form->addSubmit("submit", "Uložit")
            ->setAttribute("class", "btn btn-success");
        $form->addSubmit(self::SUBMIT_ADD_NEW, "Uložit a přidat další")
            ->setAttribute("class", "btn btn-success");
        $this->setDefaultValues($form);
        $form->onSuccess[] = function (Form $form) {
            $formSuccess = $this->productParameterFormSuccessFactory->create();
            $formSuccess->process($this, $form);
        };
        return $form;
    }



    /**
     * @param Form $form
     * @return Form
     */
    protected function setDefaultValues(Form $form)
    {
        if ($productParameterEntity = $this->getProductParameterEntity()) {
            $locale = $this->getLocale();
            $translation = $productParameterEntity->getTranslation($locale->getPrefix());

            $values['value'] = $translation->getValue();
            $values[UrlFormContainer::NAME]['url'] = $translation->getUrl();
			$values['helperId'] = $productParameterEntity->getHelperId();

            $form->setDefaults($values);
        }

        return $form;
    }



    /**
     * @throws ProductParameterFormException
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }



    /**
	 * @param $productParameterGroup ProductParameterGroupEntity
	 * @return array
    */
    private function getHelperList(ProductParameterGroupEntity $productParameterGroup) : array
	{
		$helpers = $this->helperRepo->findByKey($productParameterGroup->getFiltrationType());
		return $helpers ? Entities::toPair($helpers, 'id', 'name') : [];
	}
}