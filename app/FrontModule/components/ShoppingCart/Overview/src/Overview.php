<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\ShoppingCart\Overview;

use App\Category\CategoryFindFacadeFactory;
use App\Environment\Environment;
use App\FrontModule\Presenters\AbstractPresenter;
use App\Product\Production\Time\TimeRepository;
use App\ShoppingCart\Product\ShoppingCartProduct;
use App\ShoppingCart\Product\ShoppingCartProductDeleteFacadeException;
use App\ShoppingCart\Product\ShoppingCartProductDeleteFacadeFactory;
use App\ShoppingCart\Product\ShoppingCartProductSaveFacadeException;
use App\ShoppingCart\Product\ShoppingCartProductSaveFacadeFactory;
use App\ShoppingCart\ShoppingCartDTO;
use App\ShoppingCart\ShoppingCartFacadeException;
use Kdyby\Translation\ITranslator;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Nette\Http\Request;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Overview extends Control
{


	/** @var string http post names of values */
	const PRODUCT_HASH = 'productHash';
	const PRODUCTION_TIME_ID = 'productionTimeId';
	protected $request;

	/** @var CategoryFindFacadeFactory */
    private $categoryFindFacadeFactory;

    /** @var Context */
    private $database;

    /** @var ShoppingCartDTO|null */
    private $shoppingCart;

    /** @var ShoppingCartProductDeleteFacadeFactory */
    private $shoppingCartProductDeleteFacadeFactory;

    /** @var ShoppingCartProductSaveFacadeFactory */
    private $shoppingCartProductSaveFacadeFactory;

    /** @var TimeRepository */
    private $productionTimeRepo;

    /** @var ITranslator */
    private $translator;



    public function __construct(CategoryFindFacadeFactory $categoryFindFacadeFactory,
                                Context $context,
                                ITranslator $translator,
                                ShoppingCartProductDeleteFacadeFactory $shoppingCartProductDeleteFacadeFactory,
                                ShoppingCartProductSaveFacadeFactory $shoppingCartProductSaveFacadeFactory,
								TimeRepository $timeRepository,
								Request $request
	)
    {
		$this->request = $request;
		parent::__construct();
        $this->categoryFindFacadeFactory = $categoryFindFacadeFactory;
        $this->database = $context;
        $this->productionTimeRepo = $timeRepository;
        $this->translator = $translator;
        $this->shoppingCartProductDeleteFacadeFactory = $shoppingCartProductDeleteFacadeFactory;
        $this->shoppingCartProductSaveFacadeFactory = $shoppingCartProductSaveFacadeFactory;
    }



    /**
     * Setter for shoppingCart property.
     * @param $shoppingCart ShoppingCartDTO
     * @return self
     */
    public function setShoppingCart(ShoppingCartDTO $shoppingCart) : self
    {
        $this->shoppingCart = $shoppingCart;
        return $this;
    }



    /**
	 * Ajax handler for set production of product.
	 * @return void
	 * @throws AbortException
	 * @throws ShoppingCartFacadeException
	 * @throws InvalidArgumentException
    */
	public function handleSetProduction()
	{
		/** @var $presenter AbstractPresenter */
		$presenter = $this->getPresenter();

		if ($presenter->isAjax()) {
			$productHash = $presenter->getParameter(self::PRODUCT_HASH);
			$productionTimeId = (int)$presenter->getParameter(self::PRODUCTION_TIME_ID);

			try {
				$this->database->beginTransaction();
				$productFacade = $this->shoppingCartProductSaveFacadeFactory->create();
				$productFacade->setProduction($this->shoppingCart->getEntity()->getId(), $productHash, $productionTimeId);
				$this->database->commit();

				$this->shoppingCart = $presenter->loadShoppingCart(TRUE);
				$this->redrawControl('productBody');
				$this->redrawControl('summary');
				$presenter['opportunityForm']->redrawControl('productList');
			} catch (ShoppingCartProductSaveFacadeException $exception) {
				$this->database->rollBack();
				$presenter->payload->error = $this->translator->translate('shopping-cart.product.productionTime.errorOnSetting');
				$presenter->sendPayload();
			}
		}
	}



    /**
     * Handler for reduce quantity of product.
     * @return void
     * @throws AbortException
     * @throws ShoppingCartFacadeException
     * @throws InvalidArgumentException
     */
    public function handleReduceQuantity()
    {
        /** @var $presenter AbstractPresenter */
        $presenter = $this->getPresenter();

        if ($presenter->isAjax()) {
            $productHash = $presenter->getParameter('productHash');
            $reduce = $presenter->getParameter('reduce');

            try {
                $this->database->beginTransaction();
                $productFacade = $this->shoppingCartProductSaveFacadeFactory->create();
                $productFacade->reduceQuantity($this->shoppingCart->getEntity()->getId(), $productHash, $reduce);
                $this->database->commit();

                $this->shoppingCart = $presenter->loadShoppingCart(TRUE);
                $this->redrawControl('productBody');
                $this->redrawControl('summary');
                $presenter['opportunityForm']->redrawControl('productList');
            } catch (ShoppingCartProductSaveFacadeException $exception) {
                $this->database->rollBack();
                $presenter->payload->error = $exception->getMessage();
                $presenter->sendPayload();
            }
        }
    }



    /**
     * Handler for remove product.
     * @param $hash string product hash
     * @return void
     * @throws AbortException
     */
    public function handleRemoveProduct(string $hash)
    {
        $presenter = $this->getPresenter();
		if (!$this->shoppingCart) {
			$presenter->redirect('this');
		}

        try {
            $this->database->beginTransaction();
            $deleteFacade = $this->shoppingCartProductDeleteFacadeFactory->create();
            $deleteFacade->delete($this->shoppingCart->getEntity()->getId(), $hash);
            $this->database->commit();

            $presenter->flashMessage($this->translator->translate('shopping-cart.product.removed'), 'success');
            $presenter->redirect('this');
        } catch (ShoppingCartProductDeleteFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    /**
     * @return void
     */
    public function render()
    {
        //known limitation of Nette
        //this is because method handleReduceQuantity redraws snippets
        //and Nette calls ::render() method
		$this->renderForm();
    }



    /**
     * @return void
     */
    public function renderForm()
    {
    	$productionTimeList = $this->getProductionTimeList();
        if ($this->shoppingCart && $this->shoppingCart->hasProducts()) {
            $shoppingCartProducts = $this->shoppingCart->getProducts();

            $categories = [];
            $categoryFindFacade = $this->categoryFindFacadeFactory->create();
            foreach ($shoppingCartProducts as $product) {
            	$productId = $product->getProductId();
				$catalogProduct = $product->getCatalogProduct();
            	$productDTO = $catalogProduct ? $this->shoppingCart->getProductDTOByProductId($productId) : NULL;

				//get categories for products
                if ($productId) {
                    $_categories = $categoryFindFacade->findPublishedByProductId($productId);
                    $_categories ? $categories[$productId] = end($_categories) : NULL;
                }

                //create production form for products which has production state
				if ($productDTO && $productDTO->getState()->isProduction() === TRUE) {
                	$form = $this->createProductionForm($product, $productionTimeList);
                	$this->addComponent($form, 'productionForm_' . $productId);
				}
            }
        }

        $this->template->showMeasuringCodes = Environment::create()->showMeasuringCodes($this->getPresenter());
        $this->template->productCategories = $categories ?? [];
        $this->template->shoppingCart = $this->shoppingCart;
        $this->template->setFile(__DIR__ . '/templates/form.latte');
		$this->template->cookies_analytics = $this->request->getCookie('cookies_analytics') == "1";
		$this->template->render();
    }



    /**
     * @return void
     */
    public function renderSmall()
    {
        $this->template->shoppingCart = $this->shoppingCart;
        $this->template->setFile(__DIR__ . '/templates/small.latte');
        $this->template->render();
    }



    public function renderBar()
    {
        $this->template->shoppingCart = $this->shoppingCart;
        $this->template->setFile(__DIR__ . '/templates/bar.latte');
        $this->template->render();
    }

    public function renderRecapitulation()
    {
        $this->template->shoppingCart = $this->shoppingCart;
        $this->template->setFile(__DIR__ . '/templates/recapitulation.latte');
        $this->template->render();
    }



    /**
	 * @param $cartProduct ShoppingCartProduct
	 * @param $productionTimeList array
	 * @return Form
	 * @throws InvalidStateException
    */
    private function createProductionForm(ShoppingCartProduct $cartProduct, array $productionTimeList) : Form
	{
		$form = new Form();
		$form->addSelect('productionTime', $this->translator->translate('product.production.title'), $productionTimeList)
			->setPrompt($this->translator->translate('form.general.selectbox.prompt'))
			->setAttribute('class', 'set-production js-selectfield')
			->setAttribute('data-product-hash', $cartProduct->getHash())
			->setDefaultValue($cartProduct->getProductionTimeId() && array_key_exists($cartProduct->getProductionTimeId(), $productionTimeList) ? $cartProduct->getProductionTimeId() : NULL);
		return $form;
	}



    /**
	 * @return array
    */
    private function getProductionTimeList() : array
	{
		$list = [];
		$times = $this->productionTimeRepo->findPublished();
		foreach ($times as $time) {
			$list[$time->getId()] = $time->toString();
		}
		return $list;
	}
}