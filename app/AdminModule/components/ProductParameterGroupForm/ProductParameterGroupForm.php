<?php

declare(strict_types = 1);

namespace App\Components\ProductParameterGroupForm;

use App\Components\TranslationFormTrait;
use App\Helpers\Arrays;
use App\Helpers\Summernote;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupSaveFacadeException;
use App\ProductParameterGroup\ProductParameterGroupSaveFacadeFactory;
use App\ProductParameterGroup\ProductParameterGroupTranslationEntity;
use App\ProductParameterGroup\ProductParameterGroupTranslationSaveFacadeException;
use App\ProductParameterGroup\ProductParameterGroupTranslationSaveFacadeFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupForm extends Control
{


    /** @var string */
    const SUBMIT_ADD_NEW = "submitAddNew";

    use TranslationFormTrait;


    /** @var Context */
    protected $database;

    /**
     * @var ProductParameterGroupSaveFacadeFactory
     */
    protected $productParameterGroupSaveFacadeFactory;

    /**
     * @var ProductParameterGroupTranslationSaveFacadeFactory
     */
    protected $productParameterGroupTranslationSaveFacadeFactory;

    /** @var ProductParameterGroupEntity|null */
    protected $productParameterGroupEntity;



    public function __construct(Context $context,
                                ProductParameterGroupSaveFacadeFactory $productParameterGroupSaveFacadeFactory,
                                ProductParameterGroupTranslationSaveFacadeFactory $productParameterGroupTranslationSaveFacadeFactory)
    {
        parent::__construct();
        $this->database = $context;
        $this->productParameterGroupSaveFacadeFactory = $productParameterGroupSaveFacadeFactory;
        $this->productParameterGroupTranslationSaveFacadeFactory = $productParameterGroupTranslationSaveFacadeFactory;
    }



    /**
     * @param ProductParameterGroupEntity|null $productParameterGroupEntity
     * @return ProductParameterGroupForm
     */
    public function setProductParameterGroupEntity(ProductParameterGroupEntity $productParameterGroupEntity = NULL) : self
    {
        $this->productParameterGroupEntity = $productParameterGroupEntity;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $filtrationTypeList = Arrays::toPair(ProductParameterGroupEntity::getFiltrationTypes(), 'key', 'translation');

        $form = new Form();
        $form->addText("name", "Interní název*")
            ->setAttribute("placeholder", "Např.: Materiál šperků")
            ->setAttribute("class", "form-control")
            ->setAttribute('autofocus')
            ->setRequired("Vyplňte název skupiny.")
            ->setMaxLength(100);
        $form->addText("filtrationTitle", "Titulek pro filtraci*")
            ->setAttribute("placeholder", "Např.: Materiál")
            ->setAttribute("class", "form-control")
            ->setRequired("Vyplňte titulek pro filtraci.")
            ->setMaxLength(100);
        $form->addSelect('filtrationType', 'Typ filtrace*', $filtrationTypeList)
            ->setPrompt('- vyberte -')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyberte typ filtrace.');
        $form->addSelect('sort', 'Priorita pro produkt', $this->getSortList())
			->setAttribute('class', 'form-control')
			->setDefaultValue(1);
        $form->addTextArea('help', 'Nápověda')
            ->setAttribute('class', 'form-control')
			->setHtmlId('ckEditor')
            ->setMaxLength(1000)
            ->setEmptyValue(Summernote::EMPTY_STRING_VALUE);
        $form->addCheckbox('visibleOnProductDetail', ' Zobrazit na detailu produktu')
			->setDefaultValue(TRUE);
        $form->addCheckbox('visibleInOrder', ' Zobrazit v košíku a historii objednávek');
        $form->addSubmit("submit", "Uložit")
            ->setAttribute("class", "btn btn-success");
        $form->addSubmit(self::SUBMIT_ADD_NEW, "Uložit a přidat další")
            ->setAttribute("class", "btn btn-success");
        $this->setDefaultValues($form);
        $form->onSuccess[] = [$this, "formSuccess"];
        return $form;
    }



    /**
     * @param Form $form
     * @return Form
     */
    protected function setDefaultValues(Form $form)
    {
        if ($this->productParameterGroupEntity instanceof ProductParameterGroupEntity) {
            $locale = $this->getLocale();
            $translation = $this->productParameterGroupEntity->getTranslation($locale->getPrefix());
            $form->setDefaults([
                "name" => $translation->getName(),
                "filtrationTitle" => $translation->getFiltrationTitle(),
                'filtrationType' => $this->productParameterGroupEntity->getFiltrationType(),
                'help' => $translation->getHelp(),
				'visibleOnProductDetail' => $this->productParameterGroupEntity->getVisibleOnProductDetail(),
                'visibleInOrder' => $this->productParameterGroupEntity->getVisibleInOrder(),
				'sort' => $this->productParameterGroupEntity->getSort(),
            ]);
        }
        return $form;
    }



    /**
     * @param Form $form
     */
    public function formSuccess(Form $form)
    {
        try {
            $this->database->beginTransaction();
            $translation = $this->productParameterGroupEntity === NULL ? $this->saveNewGroup($form) : $this->updateGroup($form);
            $this->database->commit();

            $this->presenter->flashMessage("Skupina '{$translation->getName()}' byla uložena.", "success");

            if ($form->isSubmitted()->getName() == self::SUBMIT_ADD_NEW) {
                $this->presenter->redirect("ProductParameterGroup:add");
            }

            $this->presenter->redirect(":Admin:ProductParameterGroup:edit", [
                "id" => $translation->getProductParameterGroupId(),
            ]);

        } catch (ProductParameterGroupSaveFacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        } catch (ProductParameterGroupTranslationSaveFacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        }
    }



    /**
     * @param Form $form
     * @return ProductParameterGroupTranslationEntity
     */
    protected function saveNewGroup(Form $form) : ProductParameterGroupTranslationEntity
    {
        //Values
        $values = $form->getValues();

        //Save group
        $groupSaveFacade = $this->productParameterGroupSaveFacadeFactory->create();
        $groupEntity = $groupSaveFacade->save(NULL, ProductParameterGroupEntity::VARIANT_TYPE_SELECTBOX, $values->filtrationType, $values->visibleInOrder, $values->visibleOnProductDetail, $values->sort);

        //Locale
        $locale = $this->getLocale();

        //Save translation
        $groupTranslationSaveFacade = $this->productParameterGroupTranslationSaveFacadeFactory->create();
        return $groupTranslationSaveFacade->add($groupEntity, $locale->getId(), $values->name, $values->filtrationTitle, $values->help ?: NULL);
    }



    /**
     * @param Form $form
     * @return ProductParameterGroupTranslationEntity
     * @throws ProductParameterGroupFormException
     */
    protected function updateGroup(Form $form) : ProductParameterGroupTranslationEntity
    {
        if (!$this->productParameterGroupEntity instanceof ProductParameterGroupEntity) {
            throw new ProductParameterGroupFormException(sprintf("For update '%s' you must set that.", [
                ProductParameterGroupEntity::class
            ]));
        }

        //Values
        $values = $form->getValues();

        $groupSaveFacade = $this->productParameterGroupSaveFacadeFactory->create();
        $groupSaveFacade->save($this->productParameterGroupEntity->getId(), ProductParameterGroupEntity::VARIANT_TYPE_SELECTBOX, $values->filtrationType, $values->visibleInOrder, $values->visibleOnProductDetail, $values->sort);

        //Get actual translation
        $localization = $this->getLocale();
        $translation = $this->productParameterGroupEntity->getTranslation($localization->getPrefix());
        $translation->setName($values->name);
        $translation->setFiltrationTitle($values->filtrationTitle);
        $translation->setHelp($values->help ?: NULL);

        //Save translation
        $groupTranslationSaveFacade = $this->productParameterGroupTranslationSaveFacadeFactory->create();
        return $groupTranslationSaveFacade->update($translation);
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }



    /**
	 * @return int[]
    */
    private function getSortList() : array
	{
		$list = [];
		for ($i = 1; $i <= 99; $i++) {
			$list[$i] = $i;
		}
		return $list;
	}
}