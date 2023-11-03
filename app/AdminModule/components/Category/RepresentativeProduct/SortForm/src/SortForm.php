<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\RepresentativeProduct\SortForm;

use App\Category\CategoryEntity;
use App\Category\Product\Related\ProductFacadeException;
use App\Category\Product\Related\ProductFacadeFactory;
use App\Category\Product\Related\ProductRepository;
use App\Components\SortForm\SortFormFactory;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Translation\Localization;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SortForm extends Control
{


	/** @var CategoryEntity|null */
	private $category;

	/** @var Context */
	private $database;

	/** @var Localization */
	private $language;

	/** @var ProductFacadeFactory */
	private $productFacadeFactory;

	/** @var ProductRepository */
	private $productRepo;

	/** @var SortFormFactory */
	private $sortFormFactory;

	/** @var string|null */
	private $type;



	public function __construct(Context $context,
								ProductFacadeFactory $productFacadeFactory,
								ProductRepository $productRepo,
								SortFormFactory $sortFormFactory)
	{
		parent::__construct();
		$this->database = $context;
		$this->language = (new LocalizationResolver())->getActual();
		$this->productFacadeFactory = $productFacadeFactory;
		$this->productRepo = $productRepo;
		$this->sortFormFactory = $sortFormFactory;
	}



	/**
	 * @return \App\Components\SortForm\SortForm
	*/
	public function createComponentForm() : \App\Components\SortForm\SortForm
	{
		$productList = $this->getProductList();

		$sortForm = $this->sortFormFactory->create();
		$sortForm->setItems($productList);
		$sortForm->setOnSuccess([$this, 'formSuccess']);
		return $sortForm;
	}



	/**
	 * @param $form Form
	 * @param $sorting array
	 * @return void
	 * @throws AbortException
	*/
	public function formSuccess(Form $form, array $sorting)
	{
		$sorting = array_flip($sorting);
		$presenter = $this->getPresenter();

		try {
			$this->database->beginTransaction();
			$productFacade = $this->productFacadeFactory->create();
			$productFacade->saveSorting($sorting);
			$this->database->commit();

			$presenter->flashMessage('Řazení bylo uloženo.', 'success');
			$presenter->redirect('this');
		} catch (ProductFacadeException $exception) {
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
	 * @param $category CategoryEntity
	 * @return self
	 */
	public function setCategory(CategoryEntity $category) : self
	{
		$this->category = $category;
		return $this;
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
	 * @return array
	 */
	private function getProductList() : array
	{
		$productList = [];
		$products = $this->productRepo->findJoinedByCategoryIdAndTypeAndLanguageId($this->category->getId(), $this->type, $this->language->getId());
		foreach ($products as $product) {
			$productList[$product['clp_id']] = sprintf('%s - %s', $product['p_code'], $product['pt_name']);
		}
		return $productList;
	}
}