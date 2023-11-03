<?php

declare(strict_types = 1);

namespace App\Product\Command;

use App\BaseCommand;
use App\Helpers\Entities;
use App\Product\Product;
use App\Product\ProductRepository;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockFacade;
use App\ProductParameterGroup\Lock\LockFacadeFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductFeedGenerateDataCommand extends BaseCommand
{


    /** @var string */
    const COMMAND = 'product:productfeed:generatedata';

    /** @var LockFacade */
    private $parameterLockFacade;

    /** @var ProductRepository */
    private $productRepo;



    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->parameterLockFacade = $this->container->getByType(LockFacadeFactory::class)->create();
        $this->productRepo = $this->container->getByType(ProductRepository::class);
    }



    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Generate data to products for product feeds.');
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Debugger::timer();

        $maxProducts = 10000;
        $productCount = $this->productRepo->countWithMissingProductFeedData()->getCount();

        $message = sprintf('Bylo nalezeno celkem \'%d\' produktů pro vygenerování dat pro zbožové vyhledávače.', $productCount);
        $this->logger->addDebug($message);
        $output->writeln($message);
        $productsWithMissingCategoryId = [];

        for ($i = 0; $i <= $maxProducts;) {

            //prepare value
            $products = $this->productRepo->findWithMissingProductFeedData($maxProducts, $i);
            $productIds = Entities::getProperty($products, 'id');
            $googleBrands = $this->parameterLockFacade->getByKeyAndMoreProductId(Lock::GOOGLE_MERCHANT_FEED_BRAND, $productIds);
            $googleCategories = $this->parameterLockFacade->getByKeyAndMoreProductId(Lock::GOOGLE_MERCHANT_FEED_CATEGORY, $productIds);
            $heurekaCategories = $this->parameterLockFacade->getByKeyAndMoreProductId(Lock::HEUREKA_CATEGORY, $productIds);
            $zboziCzCategories = $this->parameterLockFacade->getByKeyAndMoreProductId(Lock::ZBOZI_CZ_CATEGORY, $productIds);

            foreach ($products as $product) {

                //save data
                $changed = FALSE;
                if ($googleBrand = $googleBrands[$product->getId()] ?? NULL) {
                    $product->setGoogleMerchantBrandText($googleBrand->getValue());
                    $this->logAddedData($product, Lock::GOOGLE_MERCHANT_FEED_BRAND, $googleBrand->getValue(), $output);
                    $changed = TRUE;
                }
                if ($googleCategory = $googleCategories[$product->getId()] ?? NULL) {
                    $product->setGoogleMerchantCategory($googleCategory->getValue());
                    $this->logAddedData($product, Lock::GOOGLE_MERCHANT_FEED_CATEGORY, $googleCategory->getValue(), $output);
                    $changed = TRUE;
                } else {
                    $output->writeln('Unable to determine google category for product: ' . $product->getCode());
                    $productsWithMissingCategoryId[] = $product->getCode();
                }
                if ($heurekaCategory = $heurekaCategories[$product->getId()] ?? NULL) {
                    $product->setHeurekaCategory($heurekaCategory->getValue());
                    $this->logAddedData($product, Lock::HEUREKA_CATEGORY, $heurekaCategory->getValue(), $output);
                    $changed = TRUE;
                }
                if ($zboziCzCategory = $zboziCzCategories[$product->getId()] ?? NULL) {
                    $product->setZboziCzCategory($zboziCzCategory->getValue());
                    $this->logAddedData($product, Lock::ZBOZI_CZ_CATEGORY, $zboziCzCategory->getValue(), $output);
                    $changed = TRUE;
                }
                if ($changed === FALSE) {
                    $message = sprintf(self::COMMAND. ': Produkt s id \'%d\' (%s) nebyl změněn.', $product->getId(), $product->getCode());
                    $this->logger->addDebug($message);
                    $output->writeln($message);
                    continue;
                }
                $this->productRepo->save($product);
            }

            unset($products, $googleBrands, $googleCategories, $heurekaCategories, $zboziCzCategories);
            $i += $maxProducts;
        }

        //summary message
        $message = sprintf('Bylo dokončeno přidání dat do produktů pro produktové feedy v čase \'%s\' sekund.', Debugger::timer());
        $this->logger->addInfo($message);
        $output->writeln($message);

        // $output->writeln('Products with MISSING GOOGLE CATEGORIES(' . count($productsWithMissingCategoryId) . '): ' . implode(', ', $productsWithMissingCategoryId));

        return 0;
    }



    /**
     * @param $product Product
     * @param $key string
     * @param $value string
     * @param $output OutputInterface
     * @return Product
     */
    private function logAddedData(Product $product, string $key, string $value, OutputInterface $output) : Product
    {
        $message = sprintf('Pro produkt s id \'%d\' (%s) byla doplněna hodnota \'%s\': \'%s\'. ', $product->getId(), $product->getCode(), $key, $value);
        $this->logger->addInfo($message);
        $output->writeln($message);

        return $product;
    }
}