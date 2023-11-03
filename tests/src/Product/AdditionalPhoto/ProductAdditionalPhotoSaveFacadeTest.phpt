<?php

declare(strict_types = 1);

namespace App\Tests\Product\AdditionalPhoto;

use App\Libs\FileManager\FileManager;
use App\Product\AdditionalPhoto\ProductAdditionalPhoto;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeException;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeFactory;
use App\Product\Product;
use App\Product\ProductRepository;
use App\Product\ProductRepositoryFactory;
use App\Product\Translation\ProductTranslation;
use App\Product\Translation\ProductTranslationRepository;
use App\Product\Translation\ProductTranslationRepositoryFactory;
use App\Tests\BaseTestCase;
use App\Tests\Product\ProductTestTrait;
use App\Tests\Product\Translation\ProductTranslationTestTrait;
use Nette\Http\FileUpload;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductAdditionalPhotoSaveFacadeTest extends BaseTestCase
{


    use ProductTestTrait;
    use ProductTranslationTestTrait;

    /** @var string */
    protected $testFile = __DIR__ . '/test-photo.png';

    /** @var string */
    protected $tempTestFile = __DIR__ . '/tmp_test-photo.png';

    /** @var null|ProductRepository */
    protected $productRepo;

    /** @var null|ProductTranslationRepository */
    protected $productTranslationRepo;

    /** @var FileManager */
    protected $fileManager;

    /** @var null|Product */
    protected $product;

    /** @var array|ProductTranslation[] */
    protected $translations = [];



    public function setUp()
    {
        parent::setUp();

        //save a test product
        $this->product = $product = $this->createTestProduct();

        $productRepoFactory = $this->container->getByType(ProductRepositoryFactory::class);
        $this->productRepo = $productRepoFactory->create();
        $this->productRepo->save($product);

        //save a test translation
        $this->translations[] = $translation = $this->createTestProductTranslation();
        $translation->setId($product->getId());
        $translationRepoFactory = $this->container->getByType(ProductTranslationRepositoryFactory::class);
        $this->productTranslationRepo = $translationRepoFactory->create();
        $this->productTranslationRepo->save($translation);

        $product->addTranslation($translation);

        $this->fileManager = $this->container->getByType(FileManager::class);

        //set the test dir for uploads
        $this->fileManager->setDirs([
            'baseDir' => '/upload',
            'dir' => sprintf('%s/%s', TEMP_TEST, 'upload')
        ]);
    }



    public function testSaveNew()
    {
        //prepare a temporary test file
        copy($this->testFile, $this->tempTestFile);

        $fileUpload = new FileUpload([
            'name' => 'test-photo.png',
            'type' => 'image/png',
            'size' => 999,
            'tmp_name' => $this->tempTestFile,
            'error' => 0
        ]);

        /** @var $saveFacadeFactory ProductAdditionalPhotoSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ProductAdditionalPhotoSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $photo = $saveFacade->add($this->product, [$fileUpload]);
        $photo = end($photo);

        Assert::type(ProductAdditionalPhoto::class, $photo);
        Assert::same($photo->getProductId(), $this->product->getId());
        Assert::true(file_exists(sprintf('%s/%s/%s',
            $this->fileManager->getDir()->getFullDir(),
			$this->product->getUploadFolder(),
            $photo->getFileName()
        )));
    }



    public function testUploadEmptyFile()
    {
        $fileUpload = new FileUpload([
            'name' => NULL,
            'size' => NULL,
            'tmp_name' => NULL,
            'error' => 0
        ]);

        /** @var $saveFacadeFactory ProductAdditionalPhotoSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(ProductAdditionalPhotoSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($fileUpload, $saveFacade) {
            $saveFacade->add($this->product, [$fileUpload]);
        }, ProductAdditionalPhotoSaveFacadeException::class, 'Typ souboru musí být obrázek.');
    }



    public function tearDown()
    {
        parent::tearDown();

        //delete the test product
        $this->productRepo->remove($this->product);
    }
}

(new ProductAdditionalPhotoSaveFacadeTest())->run();