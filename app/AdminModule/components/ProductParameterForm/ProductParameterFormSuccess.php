<?php

declare(strict_types = 1);

namespace App\Components\ProductParameterForm;

use App\AdminModule\Presenters\ProductParameterPresenter;
use App\Components\UrlFormContainer\UrlFormContainer;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterSaveFacadeException;
use App\ProductParameter\ProductParameterSaveFacadeFactory;
use App\ProductParameter\ProductParameterTranslationEntity;
use App\ProductParameter\ProductParameterTranslationSaveFacadeException;
use App\ProductParameter\ProductParameterTranslationSaveFacadeFactory;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use App\NObject;
use Nette\Utils\ArrayHash;
use Ricaefeliz\Mappero\Exceptions\TranslationMissingException;
use Ricaefeliz\Mappero\Translation\Localization;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterFormSuccess extends NObject
{


    /** @var Context */
    protected $database;

    /** @var ProductParameterSaveFacadeFactory */
    protected $productParameterSaveFacadeFactory;

    /** @var ProductParameterTranslationSaveFacadeFactory */
    protected $productParameterTranslationSaveFacadeFactory;



    /**
     * ProductParameterFormSuccess constructor.
     * @param $database Context
     * @param ProductParameterSaveFacadeFactory $productParameterSaveFacadeFactory
     * @param ProductParameterTranslationSaveFacadeFactory $productParameterTranslationSaveFacadeFactory
     */
    public function __construct(Context $database,
                                ProductParameterSaveFacadeFactory $productParameterSaveFacadeFactory,
                                ProductParameterTranslationSaveFacadeFactory $productParameterTranslationSaveFacadeFactory)
    {
        $this->database = $database;
        $this->productParameterSaveFacadeFactory = $productParameterSaveFacadeFactory;
        $this->productParameterTranslationSaveFacadeFactory = $productParameterTranslationSaveFacadeFactory;
    }



    /**
     * @param ProductParameterForm $productParameterForm
     * @param Form $form
     * @throws ProductParameterFormException
     * @throws TranslationMissingException
     */
    public function process(ProductParameterForm $productParameterForm, Form $form)
    {
        $productParameterEntity = $productParameterForm->getProductParameterEntity();
        $productParameterGroupEntity = $productParameterForm->getProductParameterGroupEntity();
        $localization = $productParameterForm->getLocale();
        $values = $form->getValues();
        $presenter = $productParameterForm->getPresenter();

        try {
            $this->database->beginTransaction();
            if (!$productParameterEntity instanceof ProductParameterEntity) {
                $translation = $this->saveNewParameter($values, $productParameterGroupEntity, $localization);
            } else {
                $translation = $this->updateParameter($values, $productParameterEntity, $localization);
            }
            $this->database->commit();
            $presenter->flashMessage(sprintf("Parametr '%s' byl uložen.", $translation->getValue()), "success");

            if ($form->isSubmitted()->getName() === ProductParameterForm::SUBMIT_ADD_NEW) {
                $parameters = [ProductParameterPresenter::GROUP_ID => $productParameterGroupEntity->getId()];
                $presenter->redirect("ProductParameter:add", $parameters);
            }
            $presenter->redirect("ProductParameter:edit", ["id" => $translation->getProductParameterId()]);
        } catch (ProductParameterTranslationSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), "danger");
        } catch (ProductParameterSaveFacadeException $exception) {
			$this->database->rollBack();
			$presenter->flashMessage($exception->getMessage(), "danger");
		}
    }



    /**
     * @param ArrayHash $values
     * @param ProductParameterGroupEntity $productParameterGroupEntity
     * @param Localization $localization
     * @return ProductParameterTranslationEntity
     * @throws ProductParameterTranslationSaveFacadeException
     */
    protected function saveNewParameter(ArrayHash $values,
                                        ProductParameterGroupEntity $productParameterGroupEntity,
                                        Localization $localization) : ProductParameterTranslationEntity
    {
        //Save
        $parameterSaveFacade = $this->productParameterSaveFacadeFactory->create();
        $parameter = $parameterSaveFacade->add($productParameterGroupEntity, $values->helperId ? (int)$values->helperId : NULL);

        //Save translation
        $parameterTranslationSaveFacade = $this->productParameterTranslationSaveFacadeFactory->create();
        return $parameterTranslationSaveFacade->add($parameter, $localization->getId(), $values->value, $this->getUrl($values));
    }



    /**
     * @param ArrayHash $values
     * @param ProductParameterEntity $productParameterEntity
     * @param Localization $localization
     * @return ProductParameterTranslationEntity
     * @throws TranslationMissingException
     * @throws ProductParameterTranslationSaveFacadeException
     */
    protected function updateParameter(ArrayHash $values,
                                       ProductParameterEntity $productParameterEntity,
                                       Localization $localization) : ProductParameterTranslationEntity
    {
    	$this->productParameterSaveFacadeFactory->create()->save((int)$productParameterEntity->getId(), $values->helperId ? (int)$values->helperId : NULL);

        //Get actual translation
        $translation = $productParameterEntity->getTranslation($localization->getPrefix());
        $translation->setValue($values->value);
        $translation->setUrl($this->getUrl($values));

        //Save translation
        $parameterTranslationSaveFacade = $this->productParameterTranslationSaveFacadeFactory->create();
        return $parameterTranslationSaveFacade->update($productParameterEntity, $translation);
    }



    /**
     * @param ArrayHash $values
     * @return string
     */
    protected function getUrl(ArrayHash $values) : string
    {
        return $values->{UrlFormContainer::NAME}->url;
    }

}