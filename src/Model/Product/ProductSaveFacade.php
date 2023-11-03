<?php

declare(strict_types=1);

namespace App\Product;

use App\Libs\FileManager\FileManager;
use App\NotFoundException;
use App\Product\Photo\PhotoManager;
use App\Product\WeedingRing\Gender\Gender;
use App\Product\WeedingRing\Size\SizeRepository;
use App\ProductParameter\ProductParameterNotFoundException;
use App\ProductState\ProductStateNotFoundException;
use App\ProductState\ProductStateRepositoryFactory;
use Nette\Http\FileUpload;
use App\NObject;


/**
 * todo rename to ProductFacade
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductSaveFacade extends NObject
{


    /** @var ProductDuplication */
    protected $productDuplication;

    /** @var PhotoManager */
    protected $productPhotoManager;

    /** @var ProductStateRepositoryFactory */
    protected $productStateRepoFactory;

    /** @var ProductRepository */
    protected $productRepo;

    /** @var ProductRepositoryFactory */
    protected $productRepoFactory;

    /** @var FileManager */
    protected $fileManager;

    /** @var SizeRepository */
    protected $weedingRingSizeRepo;


    public function __construct(ProductDuplication            $productDuplication,
                                PhotoManager                  $photoManager,
                                ProductStateRepositoryFactory $productStateRepoFactory,
                                ProductRepository             $productRepo,
                                ProductRepositoryFactory      $productRepoFactory,
                                FileManager                   $fileManager,
                                SizeRepository                $sizeRepository)
    {
        $this->productDuplication = $productDuplication;
        $this->productPhotoManager = $photoManager;
        $this->productStateRepoFactory = $productStateRepoFactory;
        $this->productRepo = $productRepo;
        $this->productRepoFactory = $productRepoFactory;
        $this->fileManager = $fileManager;
        $this->weedingRingSizeRepo = $sizeRepository;
    }


    /**
     * @param string $code
     * @param $externalSystemId int|null
     * @param int $stockState
     * @param int $emptyStockState
     * @param int $stock
     * @param float $price
     * @param float $vat
     * @param string $state
     * @param $newUntilTo \DateTime|null
     * @param $limitedUntilTo \DateTime|null
     * @param $bestsellerUntilTo \DateTime|null
     * @param $goodpriceUntilTo \DateTime|null
     * @param $rareUntilTo \DateTime|null
     * @param bool $saleOnline
     * @param $completed bool
     * @param $commentCompleted string|null
     * @param $type string
     * @param $discountAllowed bool
     * @return Product
     * @throws ProductSaveFacadeException
     */
    public function saveNew(string    $code,
                            int       $externalSystemId = NULL,
                            int       $stockState,
                            int       $emptyStockState,
                            int       $stock,
                            float     $price,
                            float     $vat,
                            string    $state,
                            \DateTime $newUntilTo = NULL,
                            \DateTime $limitedUntilTo = NULL,
                            \DateTime $bestsellerUntilTo = NULL,
                            \DateTime $goodpriceUntilTo = NULL,
                            \DateTime $rareUntilTo = NULL,
                            bool      $saleOnline = TRUE,
                            bool      $completed = TRUE,
                            string    $commentCompleted = NULL,
                            string    $type = Product::DEFAULT_TYPE,
                            bool      $discountAllowed = TRUE): Product
    {
        try {
            $this->checkStates($stockState, $emptyStockState);
            $productRepo = $this->productRepoFactory->create();

            $productFactory = new ProductFactory();
            $product = $productFactory->create($code, NULL, $stockState, $emptyStockState, $stock, $price, $vat,
                $state, $newUntilTo, $limitedUntilTo, $bestsellerUntilTo,
                $goodpriceUntilTo, $rareUntilTo, $saleOnline);
            $externalSystemId !== NULL ? $product->setExternalSystemId($externalSystemId) : NULL;
            $product->setType($type);
            $product->setCompleted($completed);
            $product->setCommentCompleted($commentCompleted);
            $product->setDiscountAllowed($discountAllowed);

            $this->checkDuplication($product, $productRepo);

            $productRepo->save($product);

            return $product;
        } catch (ProductStateNotFoundException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        } catch (ProductPriceException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        } catch (\EntityInvalidArgumentException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        }
    }


    /**
     * @param int $productId
     * @param $externalSystemId int|null
     * @param string $code
     * @param int $stockState
     * @param int $emptyStockState
     * @param int $stock
     * @param float $price
     * @param float $vat
     * @param string $state
     * @param $newUntilTo \DateTime|null
     * @param $limitedUntilTo \DateTime|null
     * @param $bestsellerUntilTo \DateTime|null
     * @param $goodpriceUntilTo \DateTime|null
     * @param $rareUntilTo \DateTime|null
     * @param bool $saleOnline
     * @param $completed bool
     * @param $commentCompleted string|null
     * @param $type string
     * @param $discountAllowed bool
     * @return Product
     * @throws ProductSaveFacadeException
     */
    public function update(int       $productId,
            int       $externalSystemId = NULL,
            string    $code,
            int       $stockState,
            int       $emptyStockState,
            int       $stock,
            float     $price,
            float     $vat,
            string    $state,
            \DateTime $newUntilTo = NULL,
            \DateTime $limitedUntilTo = NULL,
            \DateTime $bestsellerUntilTo = NULL,
            \DateTime $goodpriceUntilTo = NULL,
            \DateTime $rareUntilTo = NULL,
            bool      $saleOnline = TRUE,
            bool      $completed = TRUE,
            string    $commentCompleted = NULL,
            string    $type = Product::DEFAULT_TYPE,
            bool      $discountAllowed = TRUE)
    {
        try {
            $productRepo = $this->productRepoFactory->create();
            $product = $productRepo->getOneById($productId);

            $this->checkStates($stockState, $emptyStockState);

            $externalSystemId !== NULL ? $product->setExternalSystemId($externalSystemId) : NULL;
            $product->setType($type);
            $product->setCode($code);
            $product->setStockState($stockState);
            $product->setEmptyStockState($emptyStockState);
            $product->setStock($stock);
            $product->setPrice($price);
            $product->setVat($vat);
            $product->setState($state);
            $product->setNewUntilTo($newUntilTo ? $newUntilTo->format('Y-m-d') : NULL);
            $product->setLimitedUntilTo($limitedUntilTo ? $limitedUntilTo->format('Y-m-d') : NULL);
            $product->setBestsellerUntilTo($bestsellerUntilTo ? $bestsellerUntilTo->format('Y-m-d') : NULL);
            $product->setGoodpriceUntilTo($goodpriceUntilTo ? $goodpriceUntilTo->format('Y-m-d') : NULL);
            $product->setRareUntilTo($rareUntilTo ? $rareUntilTo->format('Y-m-d') : NULL);
            $product->setSaleOnline($saleOnline);
            $product->setCompleted($completed);
            $product->setCommentCompleted($commentCompleted);
            $product->setDiscountAllowed($discountAllowed);

            $this->checkDuplication($product, $productRepo);

            $productRepo->save($product);

            return $product;
        } catch (ProductNotFoundException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        } catch (ProductStateNotFoundException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        } catch (ProductPriceException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        } catch (\EntityInvalidArgumentException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        }
    }


    /**
     * Update product by external system id.
     * @param $externalSystemId int
     * @param $code string
     * @param $price float
     * @param $vat float
     * @return Product
     * @throws ProductSaveFacadeException
     */
    public function updateByExternalSystemId(int $externalSystemId, string $code, float $price, float $vat): Product
    {
        try {
            $product = $this->productRepo->getOneByExternalSystemId($externalSystemId);
            $product->setCode($code);
            $product->setPrice($price);
            $product->setVat($vat);
            $this->productRepo->save($product);

            return $product;
        } catch (ProductNotFoundException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        } catch (\EntityInvalidArgumentException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        }
    }


    /**
     * @param Product $product
     * @param FileUpload $file
     * @return Product
     * @throws ProductSaveFacadeException
     */
    public function savePhoto(Product $product, FileUpload $file)
    {
        try {
            $this->productPhotoManager->checkImage($file);
        } catch (\InvalidArgumentException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        }

        $this->productPhotoManager->delete($product);
        $name = $this->productPhotoManager->upload($product, $file);
        $product->setPhoto($name);
        $this->productRepoFactory->create()->save($product);

        return $product;
    }


    /**
     * Save settings for product search engines.
     * @param $productId int
     * @param $googleMerchantCategory string
     * @param $googleMerchantBrand string
     * @param $zboziCzCategory string
     * @param $heurekaCategory string
     * @return Product
     * @throws ProductSaveFacadeException in case of error
     */
    public function saveProductSearchEngines(int    $productId,
                                             string $googleMerchantCategory,
                                             string $googleMerchantBrand,
                                             string $zboziCzCategory,
                                             string $heurekaCategory): Product
    {
        try {
            $productRepo = $this->productRepoFactory->create();
            $product = $productRepo->getOneById($productId);
            $product->setGoogleMerchantCategory($googleMerchantCategory);
            $product->setGoogleMerchantBrandText($googleMerchantBrand);
            $product->setZboziCzCategory($zboziCzCategory);
            $product->setHeurekaCategory($heurekaCategory);

            $productRepo->save($product);
            return $product;
        } catch (ProductNotFoundException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        } catch (ProductParameterNotFoundException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        }
    }


    /**
     * @param $productId int
     * @param $quantity int
     * @return Product
     * @throws ProductSaveFacadeException
     * todo test
     */
    public function saveStockQuantity(int $productId, int $quantity): Product
    {
        try {
            $productRepo = $this->productRepoFactory->create();
            $product = $productRepo->getOneById($productId);
            $product->setStock($quantity);
            $productRepo->save($product);
            return $product;
        } catch (ProductNotFoundException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        } catch (\EntityInvalidArgumentException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        }
    }


    /**
     * @param $id int
     * @return Product
     * @throws ProductSaveFacadeException
     * todo test
     */
    public function refreshWeedingRingPairPriceById(int $id): Product
    {
        try {
            $productRepo = $this->productRepoFactory->create();
            $product = $productRepo->getOneById($id);
            $maleMinPrice = $this->weedingRingSizeRepo->getMinPriceByProductIdAndGender($product->getId(), Gender::MALE);
            $femaleMinPrice = $this->weedingRingSizeRepo->getMinPriceByProductIdAndGender($product->getId(), Gender::FEMALE);
            $product->setPrice($maleMinPrice + $femaleMinPrice);
            $productRepo->save($product);
            return $product;
        } catch (ProductNotFoundException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        } catch (NotFoundException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        } catch (\EntityInvalidArgumentException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        }
    }


    /**
     * @param int $id product id
     * @return bool
     * @throws ProductSaveFacadeException
     */
    public function delete(int $id): bool
    {
        try {
            $productRepo = $this->productRepoFactory->create();
            $product = $productRepo->getOneById($id);
            $productRepo->remove($product);
            return TRUE;
        } catch (ProductNotFoundException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        }
    }


    /**
     * @param $productId int
     * @return bool
     * @throws ProductSaveFacadeException
     */
    public function deletePhoto(int $productId): bool
    {
        try {
            $productRepo = $this->productRepoFactory->create();
            $product = $productRepo->getOneById($productId);

            if ($product->getPhoto() !== NULL) {
                $this->productPhotoManager->delete($product);
                $product->setPhoto(NULL);
                $productRepo->save($product);
            }

            return TRUE;
        } catch (ProductNotFoundException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        }
    }


    /**
     * @param $productId int
     * @param $externalSystemId int
     * @return Product
     * @throws ProductSaveFacadeException
     */
    public function amendExternalSystemId(int $productId, int $externalSystemId): Product
    {
        try {
            $productRepo = $this->productRepoFactory->create();
            $product = $productRepo->getOneById($productId);
            $productExternalId = $product->getExternalSystemId();

            if ($productExternalId !== NULL) {
                $message = sprintf('K produktu s id \'%d\' není možné doplnit externí id. Produkt již obsahuje externí id \'%d\'.', $product->getId(), $productExternalId);
                throw new ProductSaveFacadeException($message);
            }
            $duplicateProduct = $productRepo->findOneByExternalSystemId($externalSystemId);
            if ($duplicateProduct) {
                $message = sprintf('Produkt s externím id \'%d\' již existuje (id: \'%d\').', $externalSystemId, $duplicateProduct->getId());
                throw new ProductSaveFacadeException($message);
            }

            $product->setExternalSystemId($externalSystemId);
            $productRepo->save($product);

            return $product;
        } catch (ProductNotFoundException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        } catch (\EntityInvalidArgumentException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        }
    }


    /**
     * @param int $stockState
     * @param int $emptyStockState
     * @throws ProductStateNotFoundException
     */
    protected function checkStates(int $stockState, int $emptyStockState)
    {
        $stateRepo = $this->productStateRepoFactory->create();
        $stateRepo->getOneById($stockState);
        $stateRepo->getOneById($emptyStockState);
    }


    /**
     * Check if exists some product with some duplicate value.
     * @param $product Product
     * @param $productRepository ProductRepository
     * @return Product
     * @throws ProductSaveFacadeException
     */
    protected function checkDuplication(Product $product, ProductRepository $productRepository): Product
    {
        try {
            $this->productDuplication->checkByExternalSystemId($product, $productRepository);
            $this->productDuplication->checkByCode($product, $productRepository);
            return $product;
        } catch (ProductDuplicationException $exception) {
            throw new ProductSaveFacadeException($exception->getMessage());
        }
    }
}