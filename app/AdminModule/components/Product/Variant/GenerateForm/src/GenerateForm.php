<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Product\Variant\GenerateForm;

use App\Product\Parameter\ProductParameter;
use App\Product\Parameter\ProductParameterRepository AS ProductParameterRelatedRepository;
use App\Product\Product;
use App\Product\ProductFindFacadeFactory;
use App\Product\Variant\Copy\CopyFacadeException;
use App\Product\Variant\Copy\CopyFacadeFactory;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepository;
use App\ProductParameterGroup\Translation\GroupTranslationTrait;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class GenerateForm extends Control
{


	use GroupTranslationTrait;


	/** @var Context */
	private $database;

	/** @var ProductParameterRepository */
	private $groupParameterRepo;

	/** @var ProductParameterGroupTranslationRepository */
	private $groupParameterTranslationRepo;

	/** @var LocalizationResolver */
	private $localizationResolver;

	/** @var Product|null */
	private $product;

	/** @var ProductParameterGroupEntity|null */
	private $parameterGroup;

	/** @var ProductFindFacadeFactory */
	private $productFindFacadeFactory;

	/** @var ProductParameterRelatedRepository */
	private $productParameterRelatedRepo;

	/** @var CopyFacadeFactory */
	private $variantCopyFacadeFactory;



	public function __construct(Context $context,
								CopyFacadeFactory $copyFacadeFactory,
								ProductFindFacadeFactory $productFindFacadeFactory,
								ProductParameterGroupTranslationRepository $productParameterGroupTranslationRepo,
								ProductParameterRelatedRepository $productParameterRelatedRepository,
								ProductParameterRepository $productParameterRepo)
	{
		parent::__construct();
		$this->database = $context;
		$this->localizationResolver = new LocalizationResolver();
		$this->productFindFacadeFactory = $productFindFacadeFactory;
		$this->groupParameterTranslationRepo = $productParameterGroupTranslationRepo;
		$this->groupParameterRepo = $productParameterRepo;
		$this->productParameterRelatedRepo = $productParameterRelatedRepository;
		$this->variantCopyFacadeFactory = $copyFacadeFactory;
	}



	/**
	 * @param $product Product
	 * @return self
	 */
	public function setProduct(Product $product)
	{
		$this->product = $product;
		return $this;
	}



	/**
	 * @param $parameterGroup ProductParameterGroupEntity
	 * @return self
	 */
	public function setParameterGroup(ProductParameterGroupEntity $parameterGroup) : self
	{
		$this->parameterGroup = $parameterGroup;
		return $this;
	}



	/**
	 * @return Form
	 */
	public function createComponentForm() : Form
	{
		$groupParameters = $this->getGroupParameters($this->parameterGroup);
		$variantProducts = $this->getVariantProducts($this->product, $this->parameterGroup);

		$form = new Form();
		foreach ($groupParameters as $groupParameter) {
			$variantProduct = $variantProducts[$groupParameter->getId()] ?? NULL;
			$checkbox = $form->addCheckbox('check_' . $groupParameter->getId(), ' ' . $groupParameter->getTranslation()->getValue())
				->setDefaultValue($variantProduct === NULL)
				->setDisabled($variantProduct !== NULL);
			$form->addText($groupParameter->getId(), '')
				->setAttribute('class', 'form-control')
				->setDisabled($variantProduct !== NULL)
				->setDefaultValue($variantProduct ? $variantProduct->getCode() : $this->resolveCode($this->product, $groupParameter))
				->addConditionOn($checkbox, Form::FILLED)
				->setRequired('Vyplňte kód produktu.');
		}
		$form->addSubmit('submit', 'Vytvořit varianty')
			->setAttribute('class', 'btn btn-success')
			->setDisabled(count($groupParameters) === count($variantProducts));
		$form->onSuccess[] = [$this, 'formSuccess'];

		return $form;
	}



	/**
	 * @param $form Form
	 * @return void
	 * @throws AbortException
	 */
	public function formSuccess(Form $form)
	{
		$values = $this->parseFormData($form->getValues(TRUE));
		$presenter = $this->getPresenter();

		if (!$values) {
			$presenter->flashMessage('Nezvolili jste žádný parametr ke generování.', 'info');
			return ;
		}

		try {
			set_time_limit(0);

			$this->database->beginTransaction();
			$variantCopyFacade = $this->variantCopyFacadeFactory->create();
			$variantCopyFacade->copyParameterGroup($this->product->getId(), $this->parameterGroup->getId(), $values);
			$this->database->commit();

			$presenter->flashMessage('Varianty byly vytvořeny.', 'success');
			$presenter->redirect('Product:editVariant', [
				'id' => $this->product->getId(),
			]);
		} catch (CopyFacadeException $exception) {
			$this->database->rollBack();
			$presenter->flashMessage($exception->getMessage(), 'danger');
		}
	}



	/**
	 * @return Form
	 * @throws AbortException
	 */
	public function createComponentGroupForm() : Form
	{
		$localization = $this->localizationResolver->getDefault();
		$groupList = $this->getGroupList($this->groupParameterTranslationRepo, $localization);

		$form = new Form();
		$form->addSelect('parameterGroupId', 'Skupina parametrů*', $groupList)
			->setAttribute('class', 'form-control select2')
			->setPrompt('- Vyberte -')
			->setRequired('Vyberte skupinu.')
			->setDefaultValue($this->parameterGroup ? $this->parameterGroup->getId() : NULL);
		$form->addSubmit('submit', 'Vybrat')
			->setAttribute('class', 'btn btn-success');
		$form->onSuccess[] = function (Form $form) {
			$values = $form->getValues();
			$this->getPresenter()->redirect('this', [
				'parameterGroupId' => $values->parameterGroupId,
			]);
		};

		return $form;
	}



	public function render()
	{
		$this->template->parameters = $this->getGroupParameters($this->parameterGroup);
		$this->template->variants = $this->getVariantProducts($this->product, $this->parameterGroup);
		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}



	public function renderGroup()
	{
		$this->template->setFile(__DIR__ . '/templates/group.latte');
		$this->template->render();
	}



	/**
	 * @param $product Product
	 * @param $parameterGroup ProductParameterGroupEntity
	 * @return Product[]|array
	 */
	private function getVariantProducts(Product $product, ProductParameterGroupEntity $parameterGroup) : array
	{
		static $variants = [];
		if (!$variants) {
			$productFindFacade = $this->productFindFacadeFactory->create();
			$variants = $productFindFacade->findVariantsByProductIdAndParameterGroupId($product->getId(), $parameterGroup->getId());
			$productParameters = $this->getProductParameters($product, $parameterGroup);
			foreach ($productParameters as $productParameter) {
				$variants[$productParameter->getParameterId()] = $product;
			}
		}

		return $variants;
	}



	/**
	 * @param $parameterGroupEntity ProductParameterGroupEntity
	 * @return ProductParameterEntity[]|array
	 */
	private function getGroupParameters(ProductParameterGroupEntity $parameterGroupEntity) : array
	{
		static $parameters = [];
		if (!$parameters) {
			$parameters = $this->groupParameterRepo->findByMoreGroupId([$parameterGroupEntity->getId()]);
		}
		return $parameters;
	}



	/**
	 * @param $product Product
	 * @param $parameterGroupEntity ProductParameterGroupEntity
	 * @return ProductParameter[]|array
	 */
	private function getProductParameters(Product $product, ProductParameterGroupEntity $parameterGroupEntity) : array
	{
		static $parameters = [];
		if (!$parameters) {
			$parameters = $this->productParameterRelatedRepo->findByProductIdAndGroupId($product->getId(), $parameterGroupEntity->getId());
		}
		return $parameters;
	}



	/**
	 * Try create future code by set helpers.
	 * @param $product Product
	 * @param $parameter ProductParameterEntity
	 * @return string|null
	*/
	private function resolveCode(Product $product, ProductParameterEntity $parameter)
	{
		switch ($parameter->getProductParameterGroupId()) {
			case 11:
				$pattern = '/-(\d+)-/';
				if (preg_match($pattern, $product->getCode())) {
					$replace = sprintf('-%s-', $parameter->getTranslation()->getValue());
					return preg_replace($pattern, $replace, $product->getCode());
				}
				return NULL;
			default:
				return NULL;
		}
	}



	/**
	 * Parse data from sent form.
	 * @param $data array
	 * @return array
	*/
	private function parseFormData(array $data) : array
	{
		$parsedData = [];
		foreach ($data as $key => $value) {
			if ($value && is_string($key) && strpos($key, 'check_') === 0) {
				list(, $paramId) = explode('_', $key);
				$parsedData[$paramId] = $data[$paramId];
			}
		}
		return $parsedData;
	}
}