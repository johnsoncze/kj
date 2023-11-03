<?php

declare(strict_types = 1);

namespace App\Periskop\Product\Import;

use App\Diamond\Diamond;
use App\Diamond\DiamondRepository;
use App\Helpers\Entities;
use App\NotFoundException;
use App\Periskop\WeedingRing\Mapping\Mapping;
use App\Periskop\WeedingRing\Mapping\MappingRepository;
use App\Periskop\Xml\AbstractXmlCommand;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacade;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeException;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeFactory;
use App\Product\Diamond\DiamondFacade;
use App\Product\Diamond\DiamondFacadeException;
use App\Product\Diamond\DiamondFacadeFactory;
use App\Product\Parameter\ParameterStorageException;
use App\Product\Parameter\ParameterStorageFacade;
use App\Product\Parameter\ParameterStorageFacadeFactory;
use App\Product\Product;
use App\Product\ProductRepositoryFactory;
use App\Product\ProductSaveFacade;
use App\Product\ProductSaveFacadeException;
use App\Product\ProductSaveFacadeFactory;
use App\Product\Ring\Size\SizeRepository;
use App\Product\Translation\ProductTranslationSaveFacade;
use App\Product\Translation\ProductTranslationSaveFacadeException;
use App\Product\Translation\ProductTranslationSaveFacadeFactory;
use App\Product\WeedingRing\Gender\Gender;
use App\Product\WeedingRing\Size\SizeFacade;
use App\Product\WeedingRing\Size\SizeFacadeException;
use App\Product\WeedingRing\Size\SizeFacadeFactory;
use Kdyby\Monolog\Logger;
use Nette\Http\FileUpload;
use Ricaefeliz\Mappero\Translation\Localization;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;
use Tracy\ILogger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductImportCommand extends AbstractXmlCommand
{


    /** @var string base command */
    const COMMAND = 'periskop:product:import';

    /** @var string */
    const WEEDING_RING_PREFIX = 'ZPMO';

    /** @var string */
    const LOGGER_NAMESPACE = 'periskop.product.import';

    /** @var DiamondRepository */
    private $diamondRepo;

    /** @var MappingRepository */
    private $mappingRepo;

    /** @var ProductAdditionalPhotoSaveFacade|null */
    private $productAdditionalPhotoSaveFacade;

    /** @var ParameterStorageFacade */
    private $parameterStorageFacade;

    /** @var DiamondFacade */
    private $productDiamondFacade;

    /** @var ProductRepositoryFactory */
    private $productRepoFactory;

    /** @var ProductSaveFacade */
    private $productSaveFacade;

    /** @var ProductTranslationSaveFacadeFactory */
    private $productTranslationSaveFactory;

    /** @var SizeRepository */
    private $ringSizeRepo;

    /** @var SizeFacade */
    private $weedingRingSizeFacade;



    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $container = $this->getHelper('container');
        $this->diamondRepo = $container->getByType(DiamondRepository::class);
        $this->logger = $container->getByType(Logger::class);
        $this->mappingRepo = $container->getByType(MappingRepository::class);
        $this->parameterStorageFacade = $container->getByType(ParameterStorageFacadeFactory::class)->create();
        $this->productAdditionalPhotoSaveFacade = $container->getByType(ProductAdditionalPhotoSaveFacadeFactory::class)->create();
        $this->productDiamondFacade = $container->getByType(DiamondFacadeFactory::class)->create();
        $this->productRepoFactory = $container->getByType(ProductRepositoryFactory::class);
        $this->productSaveFacade = $container->getByType(ProductSaveFacadeFactory::class)->create();
        $this->productTranslationSaveFactory = $container->getByType(ProductTranslationSaveFacadeFactory::class);
        $this->ringSizeRepo = $container->getByType(SizeRepository::class);
        $this->weedingRingSizeFacade = $container->getByType(SizeFacadeFactory::class)->create();
    }



    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND)
            ->setDescription('Run import products from a file.');
    }



    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	//import numbers
        $new = 0;
        $updated = 0;
        $addedExternalSystemId = 0;
        $uploadImages = 0;
        $error = 0;

        $weedingRings = [];
        $periskopDirs = $this->container->getParameters()['periskop']['ftp'] ?? [];
        $imagesFolder = $periskopDirs['images'] ?? NULL;

        $localizationResolver = new LocalizationResolver();
        $defaultLanguage = $localizationResolver->getDefault();
        $productRepo = $this->productRepoFactory->create();
        $productSaveFacade = $this->productSaveFacade;
        $productTranslationSaveFacade = $this->productTranslationSaveFactory->create();

        Debugger::timer();

        $filePath = $input->getArgument(self::FILE_ARGUMENT);

        try {
            $xmlFile = $this->getFile($filePath);
            $items = $xmlFile->xpath('/data/items/item'); //get from xml only products
            $externalSystemIdList = $items ? $productRepo->findExternalSystemId() : [];
			$productsWithoutExternalSystemId = $items ? $productRepo->findListWithoutExternalSystemId() : [];
        } catch (\InvalidArgumentException $exception) {
        	$this->writeErrorMessage($output, $exception->getMessage(), [
        		'filePath' => $filePath,
			]);
            return 1;
        }

        foreach ($items as $item) {

            $requiredElements = ['prices/price/unit_price', 'prices/price/vat'];
            if ($this->checkRequiredElements($item, $output, $requiredElements) === TRUE) {

                $externalSystemId = (int)$item['id'];
                $stockCode = (string)$item->code;
                $code = $stockCode;
                $stock = 0;//todo doplnit do exportu
                $price = (float)$item->prices->price->unit_price;
                $vat = (float)$item->prices->price->vat;
                $name = 'Dočasný název - ' . $code;

                //only jewel with new card of stock system or another type of product
                if ($this->isNotOldJewelProductCard($stockCode) === TRUE) {
                    try {

						//weeding rings
						$pattern = '/^' . self::WEEDING_RING_PREFIX . '(\d+)(P|D)$/'; //ZPMOXXX(P|D)
						if (preg_match($pattern, $code, $matched) === 1) {
							$gender = strtolower($matched[2]);
							$_code = substr($code, 0, -1);
							$expectedGender = ['p' => Gender::MALE, 'd' => Gender::FEMALE];

							//check gender suffix
							if (array_key_exists($gender, $expectedGender) === FALSE) {
                                $invalidGenderSuffixMessage = sprintf('Snubní prsten s modelovým číslem \'%s\' obsahuje neznámý typ pohlaví \'%s\'. Očekávané typy pohlaví: %s', $code, $gender, strtoupper(implode(', ', array_keys($expectedGender))));
								$this->writeWarningMessage($output, $invalidGenderSuffixMessage);
                                Debugger::log($invalidGenderSuffixMessage, ILogger::WARNING);
								continue;
							}

							//add for later processing
							$weedingRings[$_code][$expectedGender[$gender]] = $item;
							continue;
						}

						$this->database->beginTransaction();

						//add external system id to product without that
                        if ($productsWithoutExternalSystemId && in_array($code, $productsWithoutExternalSystemId, TRUE)) {
							$productId = array_search($code, $productsWithoutExternalSystemId, TRUE);
							$product = $this->amendExternalSystemId($productId, $externalSystemId, $output);
							$externalSystemIdList[] = $externalSystemId; //for update product data in the next code
							$addedExternalSystemId++;
							unset($product);
						}

						//insert a new product
						if (!in_array($externalSystemId, $externalSystemIdList, TRUE)) {
                            $product = $productSaveFacade->saveNew($code,
                                $externalSystemId,
                                1,
                                2,
                                $stock,
                                $price,
                                $vat,
                                Product::DRAFT,
                                NULL,
                                NULL,
                                NULL,
                                NULL,
                                NULL,
                                TRUE,
                                FALSE);
                            $translation = $productTranslationSaveFacade->saveNew($product->getId(), $defaultLanguage->getId(), $name);
                            $product->addTranslation($translation);
                            $this->insertNewProductMessage($output, $externalSystemId);
                            $this->setParametersByPeriskopParameters($product, $item);
                            $new++;
                        } //update exist product
                        else {
                            $product = $productSaveFacade->updateByExternalSystemId($externalSystemId, $code, $price, $vat);
                            $this->updateProductMessage($output, $externalSystemId);
                            $updated++;
                        }

						$this->database->commit();

                        //import images
                        //todo condition '$product->getPhoto() === NULL' is temporary for upload missing photos for products without photo
                        $images = $item->xpath('images/image');
                        if ($imagesFolder && $images && $product->getPhoto() === NULL) {
                            $uploadImages = $this->saveImages($product, $images, $productSaveFacade, $imagesFolder, $output);
                        }

                        unset($product, $translation); //remove objects from ram
                    } catch (ProductSaveFacadeException $exception) {
                        $this->database->rollBack();
                        $this->processExceptionOnSave($exception, $output, $externalSystemId);
                        $error++;
                    } catch (ProductTranslationSaveFacadeException $exception) {
                        $this->database->rollBack();
                        $this->processExceptionOnSave($exception, $output, $externalSystemId);
                        $error++;
                    } catch (ParameterException $exception) {
                        $this->database->rollBack();
                        $this->processExceptionOnSave($exception, $output, $externalSystemId);
                        $error++;
                    } catch (ProductAdditionalPhotoSaveFacadeException $exception) {
                        $this->database->rollBack();
                        $this->processExceptionOnSave($exception, $output, $externalSystemId);
                        $error++;
                    } catch (SizeFacadeException $exception) {
                        $this->database->rollBack();
                        $this->processExceptionOnSave($exception, $output, $externalSystemId);
                        $error++;
                    } catch (DiamondFacadeException $exception) {
                        $this->database->rollBack();
                        $this->processExceptionOnSave($exception, $output, $externalSystemId);
                        $error++;
                    }
                }
            }
        }

        $this->importFinishedMessage($output, $filePath, Debugger::timer(), $new, $updated, $addedExternalSystemId, $error, $uploadImages);

        //save weeding rings
        $weedingRings ? $this->saveWeedingRings($weedingRings, $productSaveFacade, $productTranslationSaveFacade, $defaultLanguage, $imagesFolder, $output) : NULL;

        return 0;
    }



    /**
     * @param $weedingRings \SimpleXMLElement[][]
     * @param $productSaveFacade ProductSaveFacade
     * @param $productTranslationSaveFacade ProductTranslationSaveFacade
     * @param $localization Localization
     * @param $imagesFolder string|null
     * @param $output OutputInterface
     * @return void
     */
    private function saveWeedingRings(array $weedingRings,
                                      ProductSaveFacade $productSaveFacade,
                                      ProductTranslationSaveFacade $productTranslationSaveFacade,
                                      Localization $localization,
                                      string $imagesFolder = NULL,
                                      OutputInterface $output)
    {
        Debugger::timer();

        $new = 0;
        $updated = 0;
        $error = 0;
        $productRepo = $this->productRepoFactory->create();

        //message
		$this->writeInfoMessage($output, sprintf('Start importu snubních prstenů. Celkem \'%d\' párů k importu.', count($weedingRings)));

        foreach ($weedingRings as $code => $rings) {
            $male = $rings[Gender::MALE] ?? NULL;
            $female = $rings[Gender::FEMALE] ?? NULL;

            if ($male === NULL || $female === NULL) {
            	$message = sprintf('Pro pár snubních prstenů s modelovým číslem \'%s\' nebyl nalezen dámský či pánský model.', $code);
            	$this->writeWarningMessage($output, $message, [
            		'dámský' => $female !== NULL ? 'ok' : 'chybí',
					'pánský' => $male !== NULL ? 'ok' : 'chybí',
				]);
                continue;
            }

            try {
                $maleId = (int)$male['id'];
                $femaleId = (int)$female['id'];
                $name = (string)$female->name;
                $images = $female->xpath('images/image');

                $this->database->beginTransaction();

                try { //update
                    $mapping = $this->mappingRepo->getOneByMaleIdAndFemaleId($maleId, $femaleId);
                    $productFromStorage = $productRepo->getOneById((int)$mapping->getProductId());
                    $productTranslation = $productFromStorage->getTranslation();
                    $newUntilTo = $productFromStorage->getNewUntilTo() ? new \DateTime($productFromStorage->getNewUntilTo()) : NULL;

                    //product
                    $product = $productSaveFacade->update(
                        $productFromStorage->getId(),
                        NULL,
                        $code,
                        Product::WEEDING_RING_PAIR_TYPE_DEFAULT_STATE_ID,
                        Product::WEEDING_RING_PAIR_TYPE_DEFAULT_STATE_ID, 0,
                        (float)$productFromStorage->getPrice(), Product::WEEDING_RING_PAIR_TYPE_VAT, $productFromStorage->getState(),
                        $newUntilTo,
                        NULL,
                        NULL,
                        NULL,
                        NULL,
                        TRUE,
                        (bool)$productFromStorage->getCompleted(),
                        $productFromStorage->getCommentCompleted(), Product::WEEDING_RING_PAIR_TYPE);
                    //translation
                    $translation = $productTranslationSaveFacade->update($productTranslation->getId(), $productTranslation->getName(), $productTranslation->getDescription(), $productTranslation->getUrl(), $productTranslation->getTitleSeo(),
                        $productTranslation->getDescriptionSeo(), $productTranslation->getShortDescription(), $productTranslation->getTitleOg(), $productTranslation->getDescriptionOg());

                    unset($productFromStorage, $productTranslation, $mapping);
                    $updated++;
                } catch (NotFoundException $exception) { //new

                    //product
                    $product = $productSaveFacade->saveNew($code,
                        NULL,
                        Product::WEEDING_RING_PAIR_TYPE_DEFAULT_STATE_ID,
                        Product::WEEDING_RING_PAIR_TYPE_DEFAULT_STATE_ID,
                        0,
                        1.0,
                        Product::WEEDING_RING_PAIR_TYPE_VAT,
                        Product::DRAFT,
                        NULL,
                        NULL,
                        NULL,
                        NULL,
                        NULL,
                        TRUE,
                        FALSE,
                        NULL,
                        Product::WEEDING_RING_PAIR_TYPE);

                    //translation
                    $translation = $productTranslationSaveFacade->saveNew($product->getId(), $localization->getId(), $name, NULL, NULL, NULL, NULL);
                    $product->addTranslation($translation);

                    //save mapping
                    $mapping = new Mapping();
                    $mapping->setMaleId($maleId);
                    $mapping->setFemaleId($femaleId);
                    $mapping->setProductId($product->getId());
                    $this->mappingRepo->save($mapping);

                    $new++;
                }

                //save ring sizes
                $this->saveWeedingRingSize($product, $female, Gender::FEMALE, $output);
                $this->saveWeedingRingSize($product, $male, Gender::MALE, $output);

                //save diamonds
                $this->saveWeedingRingDiamonds($product, $female, Gender::FEMALE, $output);
                $this->saveWeedingRingDiamonds($product, $male, Gender::MALE, $output);

                //refresh price
                $productSaveFacade->refreshWeedingRingPairPriceById($product->getId());

                $this->database->commit();

				$this->writeInfoMessage($output, sprintf('Byl přidán/aktualizován pár snubních prstenů \'%s\' (ID: %d).', $code, $product->getId()));

				//save images
				if ($imagesFolder && $images && $product->getPhoto() === NULL) {
					$uploadImages = $this->saveImages($product, $images, $productSaveFacade, $imagesFolder, $output);
					unset($uploadImages);
				}

                unset($product, $translation);

            } catch (ProductSaveFacadeException $exception) {
                $this->database->rollBack();
                $this->processExceptionOnSaveWeedingRingPair($exception, $output, $code);
                $error++;
            } catch (ProductTranslationSaveFacadeException $exception) {
                $this->database->rollBack();
                $this->processExceptionOnSaveWeedingRingPair($exception, $output, $code);
                $error++;
            } catch (SizeFacadeException $exception) {
                $this->database->rollBack();
                $this->processExceptionOnSaveWeedingRingPair($exception, $output, $code);
                $error++;
            } catch (DiamondFacadeException $exception) {
                $this->database->rollBack();
                $this->processExceptionOnSaveWeedingRingPair($exception, $output, $code);
                $error++;
            } catch (DiamondException $exception) {
                $this->database->rollBack();
                $this->processExceptionOnSaveWeedingRingPair($exception, $output, $code);
                $error++;
            } catch (SizeException $exception) {
                $this->database->rollBack();
                $this->processExceptionOnSaveWeedingRingPair($exception, $output, $code);
                $error++;
            }
        }

        $this->writeInfoMessage($output, sprintf('Byl dokončen import snubních prstenů v čase %s sekund. Nových: %d. Aktualizovaných: %d. S chybou: %d', Debugger::timer(), $new, $updated, $error));
    }



    /**
     * Save weeding ring sizes.
     *
     * @param $product Product
     * @param $ring \SimpleXMLElement
     * @param $gender string
     * @param $output OutputInterface
     * @return Product
     * @throws SizeFacadeException
     * @throws SizeException
     */
    private function saveWeedingRingSize(Product $product,
                                         \SimpleXMLElement $ring,
                                         string $gender,
                                         OutputInterface $output) : Product
    {
        $from57sizeNode = $ring->xpath('attributes/attribute[@code="CENA OD"]');
        if (!$from57sizeNode) {
            throw new SizeException(sprintf('Chybí cena od velikosti 57 pro snubní prsten modelu \'%s\' (ID: %d). Určení: %s.', $product->getCode(), $product->getId(), $gender));
        }

        $sizes = $this->ringSizeRepo->findAll();
        $until56Size = (float)$ring->prices->price->unit_price;
        $from57size = (float)end($from57sizeNode)->values->value;
        $vat = (float)$ring->prices->price->vat;

        foreach ($sizes as $size) {
            $price = $size->getSize() <= 56 ? $until56Size : $from57size;
            $weedingRingSize = $this->weedingRingSizeFacade->save($product->getId(), $size->getId(), $gender, $price, $vat);

            $message = sprintf('Byla uložena cena \'%s\' pro model \'%s\' (ID: %d) snubních prstenů ve velikosti \'%s\' (ID: %d). Určení: %s.',
                $price, $product->getCode(), $product->getId(), $size->getSize(), $size->getId(), $gender);
            $this->writeInfoMessage($output, $message);

            unset($weedingRingSize);
        }

        return $product;
    }



    /**
     * @param $product Product
     * @param $ring \SimpleXMLElement
     * @param $gender string
     * @param $output OutputInterface
     * @return Product
     * @throws DiamondException
     * @throws DiamondFacadeException
     */
    private function saveWeedingRingDiamonds(Product $product,
                                             \SimpleXMLElement $ring,
                                             string $gender,
                                             OutputInterface $output) : Product
    {
        $diamondNodes = $ring->xpath('attributes/attribute[@code="VK"]');
        if ($diamondNodes) {
            $diamondsString = (string)end($diamondNodes)->values->value;
            $diamonds = strpos($diamondsString, ';') !== FALSE ? explode(';', $diamondsString) : [$diamondsString];
            $productDiamonds = $this->productDiamondFacade->findByProductIdAndGender($product->getId(), $gender);
            $productDiamonds = $productDiamonds ? Entities::setValueAsKey($productDiamonds, 'diamondId') : [];
            foreach ($diamonds as $diamond) {
                $explodedDiamond = explode('x', $diamond);
                $quantity = $explodedDiamond[0] ?? NULL;
                $size = $explodedDiamond[1] ?? NULL;
                $size = count($explodedDiamond) > 2 ? sprintf('%sx%s', $explodedDiamond[1], $explodedDiamond[2]) : $size; //means that definition is 1x1.75x1.75 (1pcs of 1.75x1.75 size)

                //check required values
                if ($quantity === NULL || $size === NULL) {
                    throw new DiamondException(sprintf('Chybí množství nebo velikost diamantů modelu \'%s\' (ID: %d) snubních prstenů. Zápis k importu: %s. Správný formát je např: 2x1.5;1x1.6;1x1.75x1.75.', $product->getCode(), $product->getId(), $diamondsString));
                }

                //save diamond
                $diamondObject = $this->getDiamondId($size);
                $diamond = $this->productDiamondFacade->save($product->getId(), $diamondObject->getId(), $gender, (int)$quantity);
                unset($productDiamonds[$diamondObject->getId()]);

                //message
                $message = sprintf('Byl uložen diamant ve velikosti \'%s\' (ID: %d) v množství \'%d\' ks pro model \'%s\' (ID: %d) snubních prstenů.', $size, $diamondObject->getId(), $quantity, $product->getCode(), $product->getId());
                $this->writeInfoMessage($output, $message);

                //remove diamond from memory
                unset($diamond);
            }

            //remove unused diamonds
            foreach ($productDiamonds as $productDiamond) {
                $this->productDiamondFacade->remove($productDiamond->getId());

                $message = sprintf('Byl smazán diamant s id \'%d\' v množství \'%d\' ks pro model \'%s\' (ID: %d) snubních prstenů. Určení: %s.',
                    $productDiamond->getDiamondId(), $productDiamond->getQuantity(), $product->getCode(), $product->getId(), $gender);
                $this->writeInfoMessage($output, $message);
            }
        }
        return $product;
    }



    /**
     * Handler for process exception which occurred on product saving.
     * @param $exception \Exception
     * @param $output OutputInterface
     * @param $id int
     * @return \Exception
     */
    private function processExceptionOnSave(\Exception $exception, OutputInterface $output, int $id) : \Exception
    {
        $message = sprintf(self::LOGGER_NAMESPACE . ': Nastala chyba při ukládání produktu s externím id %d. Chyba: %s', $id, $exception->getMessage());
        Debugger::log($message, ILogger::WARNING);
        $this->writeWarningMessage($output, $message);
        return $exception;
    }



    /**
     * @param $exception \Exception
     * @param $output OutputInterface
     * @param $code string
     * @return \Exception
     */
    private function processExceptionOnSaveWeedingRingPair(\Exception $exception, OutputInterface $output, string $code) : \Exception
    {
        $message = sprintf(self::LOGGER_NAMESPACE . ': Nastala chyba při ukládání páru snubních prstenů s modelovým číslem \'%s\'. Všechny doposud uložená data byla vrácena zpět. Chyba: %s', $code, $exception->getMessage());
        Debugger::log($message, ILogger::WARNING);
        $this->writeWarningMessage($output, $message);
        return $exception;
    }



    /**
     * Is a product with a card of new stock system?
     * @param $stockCode string
     * @return bool
     */
    private function isNotOldJewelProductCard(string $stockCode) : bool
    {
        $hasNumber = is_numeric(substr($stockCode, 0, 3));
        return !$hasNumber || $hasNumber && $this->isWatch($stockCode);
    }



    /**
     * @param $code string
     * @return bool
     */
    private function isWatch(string $code) : bool
    {
    	//$codeStarts variable contains all codes which can be passed - not only for watch
        $codeStarts = ['AI', 'CA', 'CB', 'CV', 'EL', 'FA', 'LC', 'MI', 'MP', 'T0', 'T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'PT', 'WA', 'WB', 'WBK13', '01', '08', '09', '10', '17', '20', '21', '23', '26', '27', '28', '29', '32', '34', '38', '40', '53', '54', '56', '57', '64', '70', '77', '80', '83', '84', '85', '88', '95'];
        return strpos($code, '.') !== FALSE || in_array(substr($code, 0, 2), $codeStarts);
    }


    /** ---------------- messages of import process ---------------- **/

    /**
     * Process message of finished import.
     * @param $output OutputInterface
     * @param $filePath string
     * @param $time float
     * @param $newProducts int
     * @param $updatedProducts int
	 * @param $addedExternalSystemId int
     * @param $error int count of errors
     * @param $uploadImages int
     * @return string
     */
    private function importFinishedMessage(OutputInterface $output,
                                           string $filePath,
                                           float $time,
                                           int $newProducts,
                                           int $updatedProducts,
										   int $addedExternalSystemId,
                                           int $error,
                                           int $uploadImages) : string
    {
    	$message = sprintf(self::LOGGER_NAMESPACE. ': Import produktů ze souboru \'%s\' byl dokončen v čase %s sekund.', $filePath, $time);
    	$message .= sprintf(' Nových: %d.', $newProducts);
    	$message .= sprintf(' Aktualizovaných: %d.', $updatedProducts);
    	$message .= sprintf(' Doplněných o externí id: %d.', $addedExternalSystemId);
    	$message .= sprintf(' Nahráno obrázků: %d.', $uploadImages);
    	$message .= sprintf(' Neuloženo s chybou: %d.', $error);

        $this->writeInfoMessage($output, $message);
        return $message;
    }



    /**
     * Process message of inserted a new product.
     * @param $output OutputInterface
     * @param $externalSystemId int
     * @return string
     */
    private function insertNewProductMessage(OutputInterface $output, int $externalSystemId) : string
    {
        $message = sprintf(self::LOGGER_NAMESPACE . ': Byl uložen nový produkt s externím id %d.', $externalSystemId);
        $this->writeInfoMessage($output, $message);
        return $message;
    }



    /**
     * Process message of updated a product.
     * @param $output OutputInterface
     * @param $externalSystemId int
     * @return string
     */
    private function updateProductMessage(OutputInterface $output, int $externalSystemId) : string
    {
        $message = sprintf(self::LOGGER_NAMESPACE . ': Byl aktualizován produkt s externím id %d.', $externalSystemId);
        $this->writeInfoMessage($output, $message);
        return $message;
    }



    /**
     * @param $productId int
     * @param $externalSystemId int|null
     * @param $photo string
     * @param $output OutputInterface
     * @return string
     */
    private function uploadPhotoMessage(int $productId, int $externalSystemId = NULL, string $photo, OutputInterface $output) : string
    {
        $message = sprintf(self::LOGGER_NAMESPACE . ': Byla uploadovaná fotografie \'%s\' pro produkt s id \'%s\' (externí id: %d).', $photo, $productId, $externalSystemId);
        $this->writeInfoMessage($output, $message);
        return $message;
    }



    /**
     * @param $product Product
     * @param $images \SimpleXMLElement[]
     * @param $productSaveFacade ProductSaveFacade
     * @param $imagesFolder string
     * @param $output OutputInterface
     * @return int
     */
    private function saveImages(Product $product,
                                array $images,
                                ProductSaveFacade $productSaveFacade,
                                string $imagesFolder,
                                OutputInterface $output) : int
    {
        $first = TRUE;
        $uploadImages = 0;
        foreach ($images as $image) {
            $photoId = (int)$image['id'];
            $photoName = $photoId . '.jpg';
            $photoPath = $imagesFolder . DIRECTORY_SEPARATOR . $photoName;
            $photoObject = new \SplFileInfo($photoPath);
            if ($photoObject->isFile() === TRUE) {

                //message
                $message = sprintf(self::COMMAND . ': Nalezena fotografie \'%s\' (hlavní foto: %s) produktu s id \'%d\' (externí id: %d) pro nahrání.', $photoPath, $first === TRUE ? 'ano' : 'ne', $product->getId(), $product->getExternalSystemId());
                $this->writeInfoMessage($output, $message);

                $fileValues['name'] = $photoName;
                $fileValues['type'] = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $photoPath);
                $fileValues['size'] = $photoObject->getSize();
                $fileValues['tmp_name'] = $photoPath;
                $fileValues['error'] = 0;
                $uploadObject = new FileUpload($fileValues);

                //save image
                $first === TRUE ? $productSaveFacade->savePhoto($product, $uploadObject) : $this->productAdditionalPhotoSaveFacade->add($product, [$uploadObject]);
                $this->uploadPhotoMessage($product->getId(), $product->getExternalSystemId(), $photoPath, $output);
                $uploadImages++;
            }
            $first = FALSE;
        }
        return $uploadImages;
    }



    /**
     * Temporary for watches
     * @param $product Product
     * @param $item
     * @return Product
     * @todo temporary. remove method after import watches from periskop
     */
    private function setParametersByPeriskopParameters(Product $product, \SimpleXMLElement $item) : Product
    {
        $message = sprintf('Start importu parametrů pro produkt s externím id \'%d\'.', $product->getExternalSystemId());
        $this->logger->addDebug($message, [
        	'xml' => json_decode(json_encode((array)$item), TRUE),
		]);

        $kategorie = [
            //styl hodinek (z kategorie zboží)
            'LUXUS' => 141,
            'MODNI' => 142,
            'KLAS' => 143,
            'SPORT' => 144,

            //značka hodinek (z kategorie zboží)
            'CER' => 145,
            'EDOX' => 146,
            'ML' => 147,
            'TH' => 148,
            'TISS' => 149,
	        'FREDE' => 1969, //Frederique Constant
	        'HRADO' => 1970, //Rado

            'LMTA' => [
                'EDOX' => 222,
                'ML' => 283,
                'TH' => 290,
            ],

            //určení (z kategorie zboží)
            'DARMU' => 209,
            'DARZE' => 210,

            //edox kolekce
            'LV' => 230,
            'LG' => 229,
            'DEL' => 226,
            'CHR' => 225,
            'CHS' => 223,
            'GO' => 227,
            'LAP' => 221,
            'LB' => 228,
            'ESKY' => 914,

            //maurice l kolekce
            'AI' => 240,
            'EL' => 242,
            'MA' => 237,
            'PO' => 238,
            'LC' => 239,
            'MI' => 241,
            'FI' => 243,

            //tag heuer kolekce
            'FO' => 235,
            'CA' => 232,
            'MO' => 234,
            'LI' => 236,
            'AQ' => 233,
            'HW' => 231,
			'ATIS' => 843,

			//certina světy
			'CESP' => 570,
			'CEAQ' => 743,
			'CEUR' => 744,
			'CEHE' => 745,

			//certina řady
			'P100S' => 747,
			'PCGM' => 748,
			'PGMTA' => 749,
			'PLT' => 750,
			'P10S' => 751,
			'POGM' => 752,
			'PODS' => 753,
			'POCA' => 754,
			'PO80' => 755,
			'2C10S' => 756,
			'2CFLY' => 757,
			'DS-2' => 758,
			'ECAU' => 759,
			'SC10S' => 760,
			'AP80' => 761,
			'ACTI' => 762,
			'APGM' => 763,
			'ADCA' => 764,
			'ACCH' => 765,
			'ACTIN' => 767,
			'ACTL' => 768,
			'ALAD' => 769,
			'8CMP' => 772,
			'8MP' => 773,
			'8LCH' => 774,
			'8LMP' => 775,
			'8L27' => 776,
			'8L30' => 777,
			'8P80' => 778,
			'LP80' => 779,
			'PL29' => 780,
			'PL33' => 781,
			'CAIM' => 782,
			'CAP8' => 783,
			'CAL' => 784,
			'CALP80' => 785,
			'POW8' => 786,
			'C440' => 787,
			'FLCH' => 788,
			'CSTE' => 789,
			'CTP80' => 790,
			'DSPH' => 770,
			'1P80' => 771,

            //tissot kolekce
            'TTC' => 916,
            'TTS' => 917,
            'TSPE' => 918,
            'TTO' => 919,
            'TTL' => 920,
            'TTH' => 921,
            'TTG' => 922,
            'TTP' => 923,

	        //Frederique Constant kolekce
	        'MANUF' => 1971,

	        //Rado
	        'CCOOK' => 1972, //Captain Cook
	        'CENTR' => 1973, //Centrix
	        'COUPL' => 1978, //Couple Classic
	        'HYCHR' => 1977, //HyperChrome
	        'INTEG' => 1979, //Integral
	        'TRUE' => 1974, //True
	        'STRUE' => 1975, //True Square
	        'TRUET' => 1976, //True Thinline

            'DIAST' => 2006, //Diastar Original
            'DIMAS' => 2007, //Diamaster
            'CERAM' => 2008, //Ceramica
            'HORSE' => 2009, //Golden Horse
            'CHYPE' => 2010, //HyperChrome Classic
            'FLORE' => 1991, //Florence Classic

            //edice
            'SPEC' => 1838,
            'LIMTA' => 1837,

        ];

        if ($this->isWatch($product->getCode()) !== TRUE) {
            $this->logger->addNotice(sprintf('Pro produkt \'%s\' nebyly importovány parametery. Nejedná se o hodinky.', $product->getCode()));
            return $product;
        }

        try {

            $defaultParameters = [252];
            foreach ($defaultParameters as $parameter) {
                $productParameter = $this->parameterStorageFacade->add($product->getId(), $parameter);
                $this->logAddedParameter($product, $parameter);
                unset($productParameter);
            }

            //data from category
            $i = 0;
            $usedCategories = [];
            $categories = (array)$item->groups;
            $lmtaCode = 'LMTA';
            foreach ($categories as $_group) {
                foreach ($_group as $group) {
                    $code = (string)$group['code'];
                    if ($code === $lmtaCode) {
                        $parameterId = NULL;
                        foreach ($usedCategories as $category) {
                            if (isset($kategorie[$lmtaCode][$category])) {
                                $parameterId = $kategorie[$lmtaCode][$category];
                            }
                        }
                        if ($parameterId === NULL) {
                            throw new ParameterException('Nelze nastavit limitovanou edici. Chybí kód značky nebo id parametru.');
                        }
                    } else {
                        $parameterId = $kategorie[$code] ?? NULL;
                    }

                    if ($parameterId !== NULL) {
                        $usedCategories[] = $code;
                        $productParameter = $this->parameterStorageFacade->add($product->getId(), $parameterId);
                        $this->logAddedParameter($product, $parameterId);
                        unset($productParameter);
                        $i++;
                    }
                }
            }

            //data from item attributes
            $attributes = (array)$item->attributes;
            foreach ($attributes as $attribute) {
                foreach ($attribute as $a) {
                    $code = (string)$a['code'];
                    $value = mb_strtolower((string)$a->values->value);

                    //parse values which contains "/" character
                    $exceptions = [
                    	'PAS' => [
                    		'ocel / pvd' => TRUE,
						],
                        'POU' => [
                            'ocel / zlacené' => TRUE,
                            'ocel / pvd' => TRUE,
                        ],
                        'CIFB' => [
                            'Stříbro 925 / zlacené ručičky' => TRUE,
                        ],
                    ];
                    if (!isset($exceptions[$code][$value]) && strpos($value, '/') !== FALSE) {
                        $values = explode('/', $value);
                        foreach ($values as $value) {
                            $value = trim($value);
                            $parameterId = $this->getAttributeId($code, $value);
                            if ($parameterId !== NULL) {
                                $productParameter = $this->parameterStorageFacade->add($product->getId(), $parameterId);
                                $this->logAddedParameter($product, $parameterId);
                                unset($productParameter);
                            }
                        }
                        continue;
                    }

                    $parameterId = $this->getAttributeId($code, $value);
                    if ($parameterId !== NULL) {
                        $productParameter = $this->parameterStorageFacade->add($product->getId(), $parameterId);
                        $this->logAddedParameter($product, $parameterId);
                        unset($productParameter);
                    }
                }
            }

            return $product;
        } catch (ParameterStorageException $exception) {
            throw new ParameterException($exception->getMessage());
        }
    }



    /**
     * @todo temporary. remove method after import watches from periskop
     */
    private function getAttributeId($code, $value)
    {
        $exceptions = ['CHR/ne', 'DAT/ne'];
        $vlastnosti = [
        	'Funkce' => [
        		'budík' => 590,
			],

            //pohon (z vlastností)
            'TYP' => [
                'automat' => 150,
                'mechanic' => 151,
                'quartz' => 152,
                'solár' => 153,
            ],

            //funkce (z vlastností)
            'CHR' => [
                'ano' => 183,
            ],
            'DAT' => [
                'ano' => 184,
            ],
            'KAL' => [
                'ano' => 185,
            ],

            //průměr pozdra (z vlastností)
            'POUP' => [
                '20 mm' => 930,
                '22 mm' => 198,
                '24 mm' => 931,
                '25 mm' => 929,
                '27 mm' => 593,
                '28 mm' => 199,
                '29 mm' => 343,
                '30 mm' => 262,
                '31 mm' => 801,
                '32 mm' => 200,
                '33 mm' => 288,
                '33,8 mm' => 817,
                '33.8 mm' => 817,
                '34 mm' => 732,
                '35 mm' => 266,
                '36 mm' => 201,
                '37 mm' => 345,
                '38 mm' => 202,
                '39 mm' => 203,
                '40 mm' => 204,
                '40,5 mm' => 591,
                '41 mm' => 205,
                '42 mm' => 206,
                '43 mm' => 207,
                '44 mm' => 256,
                '45 mm' => 208,
                '45,6 mm' => 819,
                '46 mm' => 331,
                '48 mm' => 330,
                '49 mm' => 933,
                '50 mm' => 934,
                '51 mm' => 932,
                '20,9 x 39 mm' => 282,
                '20 x 35 mm' => 291,
                '31,5 x 34,5 mm' => 347,
            ],

            //vodotěsnost
            'VOD' => [
                '100 m' => 192,
                '150 m' => 928,
                '1000 m' => 197,
                '200 m' => 193,
                '30 m' => 190,
                '300 m' => 194,
                '400 m' => 195,
                '50 m' => 191,
                '500 m' => 196,
                '600 m' => 332,
            ],

            //kámen v hodinkách
            'DIA' => [
                'ano' => 187,
            ],

            //řemínek
            'PAS' => [
                'kaučuk' => 158,
				'keramika' => 583,
                'kůže' => 155,
                'ocel' => 154,
                'ocel / pvd' => 160,
                'pryž' => 159,
                'silikon' => 156,
                'textil' => 157,
                'nylon' => 273,
                'denim' => 274,
                'zlacený' => 278,
                'krokodýlí kůže' => 281,
                'růžové zlato 18 kt' => 586,
                'satinovaná kůže' => 284,
				'titan' => 584,
                'zlacení' => 278,
				'žluté zlato 18 kt' => 572
            ],

            //sklo
            'SKL' => [
                'safírové / antireflex' => 218,
                'safírové' => 161,
                'safírové / double antireflex' => 250,
                'SKLM' => 162,
                'antireflexní' => 279,
                'antireflex' => 279,
                'double antireflexní' => 280,
                'double antireflex' => 280,
                'akrylové' => 287,
                'minerální' => 162,
            ],

            //pouzdro
            'POU' => [
                'ocel' => 163,
                'hliník' => 164,
                'titan' => 165,
                'keramika' => 167,
                'ocel / pvd' => 166,
                'pvd' => 166,
                'powerlite®' => 168,
                'žluté zlato 18kt' => 276,
                'zlacené' => 277,
                'bronz' => 285,
                'zlacení' => 277,
                'růžové zlato 18 kt' => 322,
				'žluté zlato 18 kt' => 276,
                'karbon' => 328,
            ],

            //barva řemínku
            'PASB' => [
                'antracitová' => 585,
                'béžová' => 292,
                'hnědá' => 169,
				'khaki' => 589,
                'černá' => 170,
                'stříbrná' => 171,
                'zlatá' => 172,
                'červená' => 173,
                'ocelová' => 174,
                'bílá' => 175,
                'růžová' => 212,
                'fialová' => 216,
                'modrá' => 253,
                'oranžová' => 254,
                'tyrkysová' => 271,
                'šedá' => 293,
                'zelená' => 344,
                'duhová' => 1760,
            ],

            //barva číselníku
            'CIFB' => [
                'antracitová' => 260,
                'béžová' => 342,
                'bílá' => 244,
                'bílý' => 244,
                'černá' => 176,
                'černá perleť' => 320,
                'černý' => 176,
                'červená' => 213,
                'červený' => 213,
                'dakarský písek' => 346,
                'fialová' => 215,
                'fialový' => 215,
                'hnědá' => 177,
                'hnědý' => 177,
				        'khaki' => 588,
                'modrá' => 182,
                'modrý' => 182,
                'modrá perleť' => 217,
                'perleťová' => 180,
                'růžová' => 211,
				        'růžová perleť' => 587,
                'růžový' => 211,
                'oranžová' => 518,
                'šedá' => 181,
                'šedý' => 181,
                'stříbrná' => 178,
                'stříbrný' => 178,
                'tyrkysová' => 270,
                'tyrkysový' => 270,
                'zelená' => 272,
                'zelený' => 272,
                'zlatá' => 179,
                'zlatý' => 179,
                'stříbro 925' => 333,
                'stříbro 925 modře lakované' => 324,
                'stříbro 925 černě lakované' => 325,
                'stříbro 925 / zlacené ručičky' => 326,
                'rhodiový číselník' => 327,
                'stříbrná / zlacené ručičky' => 329,
                'skelet' => 286,
                'zlacené ručičky' => 334,
                'duhová' => 1761,
            ],
        ];

        if (isset($vlastnosti[$code])) {
            if (in_array("$code/$value", $exceptions) === FALSE) {
                $value = $code === 'DIA' ? 'ano' : $value; //todo create mapping table for values
                $parameterId = $vlastnosti[$code][$value] ?? NULL;
                if ($parameterId === NULL) {
                    throw new ParameterException(sprintf(self::LOGGER_NAMESPACE . '.parameter: Chybí parameted id pro vlastnost \'%s\' s hodnotou \'%s\'.', $code, $value));
                }
                return $parameterId;
            }
        }
        return NULL;
    }



    private function logAddedParameter(Product $product, $parameterId) : Product
    {
        $this->logger->addInfo(sprintf('Pro produkt s externím id \'%d\' byl přidán parametr s id \'%d\'.', $product->getExternalSystemId(), $parameterId));
        return $product;
    }



    /**
     * @param $size string
     * @return Diamond
     * @throws DiamondException
     */
    private function getDiamondId(string $size) : Diamond
    {
        static $diamondList = [];
        if (!$diamondList) {
            $diamonds = $this->diamondRepo->findAll();
            $diamondList = $diamonds ? Entities::setValueAsKey($diamonds, 'size') : [];
        }
        $diamond = $diamondList[$size] ?? NULL;
        if ($diamond === NULL) {
            throw new DiamondException(sprintf('Chybí definice diamantu pro velikost \'%s\'.', $size));
        }
        return $diamond;
    }



    /**
	 * Amend external system id to product without that.
	 * @param $productId int
	 * @param $externalSystemId int
	 * @param $output OutputInterface
	 * @return Product
	 * @throws ProductSaveFacadeException
    */
    private function amendExternalSystemId(int $productId, int $externalSystemId, OutputInterface $output) : Product
	{
		$product = $this->productSaveFacade->amendExternalSystemId($productId, $externalSystemId);
		$this->writeInfoMessage($output, sprintf(self::LOGGER_NAMESPACE . ': K produktu s id \'%d\' bylo doplněno externí id \'%d\'.', $product->getId(), $externalSystemId));

		return $product;
	}
}

class DiamondException extends \Exception
{


}

class ParameterException extends \Exception
{


}

class SizeException extends \Exception
{


}
