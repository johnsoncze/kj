<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product;

use App\NotFoundException;
use App\Product\ProductDTOFactory;
use App\Product\Production\Time\TimeRepository;
use App\Product\ProductNotFoundException;
use App\Product\ProductPublishedRepositoryFactory;
use App\ShoppingCart\ShoppingCartNotFoundException;
use App\ShoppingCart\ShoppingCartRepositoryFactory;
use App\ShoppingCart\ShoppingCartTranslation;
use Kdyby\Translation\ITranslator;
use Nette\Database\UniqueConstraintViolationException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartProductSaveFacade
{


    /** @var Discount */
    protected $discount;

    /** @var ProductDTOFactory */
    protected $productDTOFactory;

    /** @var ShoppingCartRepositoryFactory */
    protected $shoppingCartRepoFactory;

    /** @var ProductPublishedRepositoryFactory */
    protected $productPublishedRepositoryFactory;

    /** @var TimeRepository */
    protected $productionTimeRepo;

    /** @var ShoppingCartProductRepositoryFactory */
    protected $shoppingCartProductRepoFactory;

    /** @var ITranslator $translator */
    protected $translator;



    public function __construct(Discount $discount,
								ProductDTOFactory $productDTOFactory,
								TimeRepository $timeRepository,
                                ShoppingCartRepositoryFactory $shoppingCartRepoFactory,
                                ProductPublishedRepositoryFactory $productPublishedRepositoryFactory,
                                ShoppingCartProductRepositoryFactory $shoppingCartProductRepoFactory,
                                ITranslator $translator)
    {
        $this->discount = $discount;
        $this->productDTOFactory = $productDTOFactory;
        $this->productionTimeRepo = $timeRepository;
        $this->shoppingCartRepoFactory = $shoppingCartRepoFactory;
        $this->productPublishedRepositoryFactory = $productPublishedRepositoryFactory;
        $this->shoppingCartProductRepoFactory = $shoppingCartProductRepoFactory;
        $this->translator = $translator;
    }



    /**
     * @param int $shoppingCartId
     * @param int $productId
     * @param int $quantity
	 * @param $productionTimeId int|null
     * @return ShoppingCartProduct
     * @throws ShoppingCartProductSaveFacadeException
     */
    public function save(int $shoppingCartId, int $productId, int $quantity, int $productionTimeId = NULL) : ShoppingCartProduct
    {
        try {
            $cartRepo = $this->shoppingCartRepoFactory->create();
            $shoppingCartProductRepo = $this->shoppingCartProductRepoFactory->create();
            $cart = $cartRepo->getOneById($shoppingCartId, $this->translator);

            $productRepo = $this->productPublishedRepositoryFactory->create();
            $product = $productRepo->getOneById($productId, $this->translator);
            $productsDTO = $this->productDTOFactory->createFromProducts([$product]);
            $productDTO = end($productsDTO);

            if ($product->isInStock() !== TRUE && $productDTO->getState()->isProduction() !== TRUE) {
                throw new ShoppingCartProductSaveFacadeException($this->translator->translate('product.stock.zero'));
            }
            if ($productionTimeId === NULL && $productDTO->getState()->isProduction() === TRUE) {
            	throw new ShoppingCartProductSaveFacadeException($this->translator->translate('product.production.missing'));
			}

			try {
            	$productionTime = $productionTimeId ? $this->productionTimeRepo->getOnePublishedById($productionTimeId) : NULL;
			} catch (NotFoundException $exception) {
            	$message = $this->translator->translate('shopping-cart.product.productionTime.notAvailable', ['product' => $product->getTranslation()->getName()]);
            	throw new ShoppingCartProductSaveFacadeException($message);
			}

            try {
                //try load product from cart if is exists already
                $shoppingCartProduct = $shoppingCartProductRepo->getOneByCartIdAndProductId($shoppingCartId, $productId);
                $quantity = $shoppingCartProduct->getQuantity() + $quantity;
				$this->discount->applyDiscount($cart, $shoppingCartProduct);
            } catch (ShoppingCartProductNotFoundException $exception) {
                $shoppingCartProductFactory = new ShoppingCartProductFactory();
                $shoppingCartProduct = $shoppingCartProductFactory->createFromProduct($product, $cart, $quantity);
                $this->discount->applyDiscount($cart, $shoppingCartProduct);
                $this->setHash($shoppingCartProduct);
            }

            if ($productDTO->getState()->isProduction() !== TRUE && $product->hasEnoughQuantity($quantity) !== TRUE) {
                $message = $shoppingCartProduct->getId()
                    ? $this->translator->translate('shopping-cart.product.maximumQuantityAndProductInCart', ['name' => $shoppingCartProduct->getTranslatedName(), 'quantity' => $product->getStock(), 'cartQuantity' => $shoppingCartProduct->getQuantity()])
                    : $this->translator->translate('shopping-cart.product.maximumQuantity', ['name' => $shoppingCartProduct->getTranslatedName(), 'quantity' => $product->getStock()]);
                throw new ShoppingCartProductSaveFacadeException($message);
            }

			$productionTime ? $shoppingCartProduct->setProductionTimeId($productionTime->getId()) : NULL;
            $shoppingCartProduct->setQuantity($quantity, $this->translator);
            $shoppingCartProduct->setPrice($product->getPrice());
            $shoppingCartProduct->setVat($product->getVat());
            $shoppingCartProduct->setAddDate(new \DateTime());
            $shoppingCartProductRepo->save($shoppingCartProduct);

            return $shoppingCartProduct;
        } catch (ShoppingCartNotFoundException $exception) {
            throw new ShoppingCartProductSaveFacadeException($this->translator->translate(sprintf('%s.action.failed', ShoppingCartTranslation::getFileName())));
        } catch (ProductNotFoundException $exception) {
            throw new ShoppingCartProductSaveFacadeException($exception->getMessage());
        } catch (UniqueConstraintViolationException $exception) {
            throw new ShoppingCartProductSaveFacadeException($this->translator->translate(sprintf('%s.action.failed', ShoppingCartTranslation::getFileName())));
        } catch (WrongQuantityException $exception) {
            throw new ShoppingCartProductSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $cartId int
     * @param $productHash string
     * @param $action string
     * @return ShoppingCartProduct
     * @throws ShoppingCartProductSaveFacadeException
     */
    public function reduceQuantity(int $cartId, string $productHash, string $action) : ShoppingCartProduct
    {
        try {
            $shoppingCartProductRepo = $this->shoppingCartProductRepoFactory->create();
            $shoppingCartProduct = $shoppingCartProductRepo->getOneByShoppingCartIdAndHash($cartId, $productHash, $this->translator);
            $shoppingCartProduct->reduceQuantity($action);

            $catalogProduct = $shoppingCartProduct->getCatalogProduct();
            if (!$catalogProduct) {
                throw new ShoppingCartProductSaveFacadeException($this->translator->translate('shopping-cart.product.unavailable', ['name' => $shoppingCartProduct->getTranslatedName()]));
            }
            $productsDTO = $this->productDTOFactory->createFromProducts([$catalogProduct]);
            $productDTO = end($productsDTO);
            if ($productDTO->getState()->isProduction() !== TRUE && $catalogProduct->hasEnoughQuantity($shoppingCartProduct->getQuantity()) !== TRUE) {
                throw new ShoppingCartProductSaveFacadeException($this->translator->translate('shopping-cart.product.maximumQuantity', ['name' => $shoppingCartProduct->getTranslatedName(), 'quantity' => $catalogProduct->getStock()]));
            }

            $shoppingCartProductRepo->save($shoppingCartProduct);

            return $shoppingCartProduct;
        } catch (ShoppingCartProductNotFoundException $exception) {
            throw new ShoppingCartProductSaveFacadeException($exception->getMessage());
        } catch (WrongQuantityException $exception) {
            throw new ShoppingCartProductSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * @param int $shoppingCartProductId
     * @param int $quantity
     * @return ShoppingCartProduct
     * @throws ShoppingCartProductSaveFacadeException
     * todo a co sklad?:-)
     */
    public function update(int $shoppingCartProductId, int $quantity) : ShoppingCartProduct
    {
        try {
            $shoppingCartProductRepo = $this->shoppingCartProductRepoFactory->create();
            $shoppingCartProduct = $shoppingCartProductRepo->getOneById($shoppingCartProductId, $this->translator);

            $quantityService = new ShoppingCartProductQuantity();
            $quantityService->setQuantity($shoppingCartProduct, $quantity, $this->translator);

            $shoppingCartProductRepo->save($shoppingCartProduct);

            return $shoppingCartProduct;
        } catch (ShoppingCartProductNotFoundException $exception) {
            throw new ShoppingCartProductSaveFacadeException($exception->getMessage());
        } catch (WrongQuantityException $exception) {
            throw new ShoppingCartProductSaveFacadeException($exception->getMessage());
        }
    }



    /**
	 * @param $cartId int
	 * @param $productHash string
	 * @param $productionTimeId int
	 * @return ShoppingCartProduct
	 * @throws ShoppingCartProductSaveFacadeException
	 * todo test
    */
	public function setProduction(int $cartId, string $productHash, int $productionTimeId) : ShoppingCartProduct
	{
		try{
			$cartProductRepo = $this->shoppingCartProductRepoFactory->create();
			$cart = $this->shoppingCartRepoFactory->create()->getOneById($cartId, $this->translator);
			$cartProduct = $cartProductRepo->getOneByShoppingCartIdAndHash($cart->getId(), $productHash, $this->translator);
			$productionTime = $this->productionTimeRepo->getOnePublishedById($productionTimeId);
			$cartProduct->setProductionTimeId($productionTime->getId());
            $cartProduct->setAddDate(new \DateTime());
			$cartProductRepo->save($cartProduct);

			return $cartProduct;
		} catch (ShoppingCartNotFoundException $exception) {
			throw new ShoppingCartProductSaveFacadeException($exception->getMessage());
		} catch (ShoppingCartProductNotFoundException $exception) {
			throw new ShoppingCartProductSaveFacadeException($exception->getMessage());
		} catch (NotFoundException $exception) {
			throw new ShoppingCartProductSaveFacadeException($exception->getMessage());
		}
	}



    /**
     * @param ShoppingCartProduct $product
     * @return ShoppingCartProduct
     */
    protected function setHash(ShoppingCartProduct $product) : ShoppingCartProduct
    {
        $hash = new ShoppingCartProductHash();
        $hash->setHash($product, $hash::generateHash());

        return $product;
    }

}