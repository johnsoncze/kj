<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\Collection\Preview;

use App\Category\CategoryEntity;
use App\Category\CategoryRepository;
use App\CategoryProductParameter\CategoryProductParameterRepository;
use App\FrontModule\Components\Company\Preview\PreviewFactory;
use App\Product\Product;
use App\Product\ProductFindFacadeFactory;
use App\Product\ProductPublishedRepository;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockFacadeFactory;
use Kdyby\Monolog\Logger;
use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;
use App\Helpers\Entities;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Preview extends Control
{


    /** @var CategoryRepository */
    private $categoryRepo;

    /** @var PreviewFactory */
    private $companyPreviewFactory;

    /** @var LockFacadeFactory */
    private $lockFacadeFactory;

    /** @var Logger */
    private $logger;

    /** @var Product|null */
    private $product;

    /** @var ITranslator */
    private $translator;

    /** @var CategoryProductParameterRepository */
    public $categoryParameterRepo;

    /** @var ProductPublishedRepository */
    public $productRepo;

    /** @var ProductFindFacadeFactory */
    public $productFindFacadeFactory;


    public function __construct(CategoryRepository $categoryRepository,
								ITranslator $translator,
								Logger $logger,
                                LockFacadeFactory $lockFacadeFactory,
                                ProductPublishedRepository $productRepo,
                                ProductFindFacadeFactory $productFindFacadeFactory,
                                CategoryProductParameterRepository $categoryParameterRepo,
								PreviewFactory $previewFactory)
    {
        parent::__construct();
        $this->categoryRepo = $categoryRepository;
        $this->lockFacadeFactory = $lockFacadeFactory;
        $this->logger = $logger;
        $this->companyPreviewFactory = $previewFactory;
        $this->translator = $translator;
        $this->productRepo = $productRepo;
        $this->categoryParameterRepo = $categoryParameterRepo;
        $this->productFindFacadeFactory = $productFindFacadeFactory;
    }



    /**
     * @param $product Product
     * @return self
     */
    public function setProduct(Product $product) : self
    {
        $this->product = $product;
        return $this;
    }


    public function render()
    {
        $this->template->collection = $collection = $this->getCollection($this->product);
		$this->template->collectionData = $collection ? $this->getCollectionData($collection) : [];

		if($collection){
        $categoryParameters = $this->categoryParameterRepo->findByCategoryId($collection->getId());
        //todo move search product logic to ProductFindFacade class
        if ($categoryParameters) {
            $parameterId = Entities::getProperty($categoryParameters, 'productParameterId');
            $categoryProductId = $this->productRepo->findProductIdByMoreParameterIdAsCategoryParameter($parameterId);
            if ($categoryProductId) {
                $productFindFacade = $this->productFindFacadeFactory->create();
                $groupedCategoryProductId = $productFindFacade->findProductIdWithGroupedVariantsByMoreProductIdAndMoreParameterId($categoryProductId, $filter['parameterWithGroup'] ?? []);
                $productCount = count($groupedCategoryProductId);
                $this->template->sameCategoryProducts = $productFindFacade->findPublishedByMoreIdAndLimitAndOffset($groupedCategoryProductId, 3, rand(0,$productCount-3), [], $collection->getId());

            }
        }
        }

        $this->template->setFile(__DIR__ . '/templates/default.latte');
        $this->template->render();
    }



    public function renderSmallText()
    {
		$this->template->collection = $this->getCollection($this->product);

        $this->template->setFile(__DIR__ . '/templates/smallText.latte');
        $this->template->render();
    }
    public function renderAsParameter()
    {
		$this->template->collection = $this->getCollection($this->product);

        $this->template->setFile(__DIR__ . '/templates/asParameter.latte');
        $this->template->render();
    }
    public function renderDescription()
    {
		$this->template->collection = $this->getCollection($this->product);

        $this->template->setFile(__DIR__ . '/templates/description.latte');
        $this->template->render();
    }



    /**
	 * @return \App\FrontModule\Components\Company\Preview\Preview
    */
    public function createComponentCompanyPreview() : \App\FrontModule\Components\Company\Preview\Preview
	{
		return $this->companyPreviewFactory->create();
	}



    /**
     * @param $product Product
     * @return CategoryEntity|null
     */
    private function getCollection(Product $product)
    {
        $lockFacade = $this->lockFacadeFactory->create();
        $parameter = $lockFacade->findOneValueByKeyAndProductId(Lock::PRODUCT_COLLECTION_PREVIEW, $product->getId());
        return $parameter ? $this->categoryRepo->findOnePublishedById((int)$parameter) : NULL;
    }



    /**
	 * @param $category CategoryEntity
	 * @return array
    */
    private function getCollectionData(CategoryEntity $category) : array
	{
		//category id => key
		$mapping = [
			15 => [
				'description' => $this->translator->translate('collection.andele.description1'),
				'image' => '/www/assets/front/user_content/images/collection/andele/andel-bg-kolekce-detail-1070x570.jpg',
			],
			17 => [
				'description' => $this->translator->translate('collection.classic.description1'),
				'image' => '/www/assets/front/user_content/images/collection/classic/classic-bg-kolekce-detail-1060x570.jpg',
			],
			18 => [
				'description' => $this->translator->translate('collection.denni.description1'),
				'image' => '/www/assets/front/user_content/images/collection/denni/denni-bg-kolekce-detail-1060x570.png',
			],
			19 => [
				'description' => $this->translator->translate('collection.detska.description1'),
				'image' => '/www/assets/front/user_content/images/collection/detska/detska-bg-kolekce-detail-1060x570.jpg',
			],
			22 => [
				'description' => $this->translator->translate('collection.kvetiny.description1'),
				'image' => '/www/assets/front/user_content/images/collection/kvetiny/kvetiny-bg-kolekce-detail-1060x570.jpg',
			],
			23 => [
				'description' => $this->translator->translate('collection.laskaVira.description1'),
				'image' => '/www/assets/front/user_content/images/collection/laskaviranadeje/laskaviranadeje-bg-kolekce-detail-1060x570.jpg',
			],
			24 => [
				'description' => $this->translator->translate('collection.nasiMotyli.description1'),
				'image' => '/www/assets/front/user_content/images/collection/motyli/motyli-bg-kolekce-detail-1060x570.jpg',
			],
			26 => [
				'description' => $this->translator->translate('collection.organickeMotivy.description1'),
				'image' => '/www/assets/front/user_content/images/collection/organickemotivy/organicke-bg-kolekce-detail-1060x570.jpg',
			],
			27 => [
				'description' => $this->translator->translate('collection.perly.description1'),
				'image' => '/www/assets/front/user_content/images/collection/perly/perly-bg-kolekce-detail-1060x570.png',
			],
			28 => [
				'description' => $this->translator->translate('collection.primavera.description1'),
				'image' => '/www/assets/front/user_content/images/collection/primavera/primavera-bg-kolekce-detail-1060x570.jpg',
			],
			29 => [
				'description' => $this->translator->translate('collection.radost.description1'),
				'image' => '/www/assets/front/user_content/images/collection/radost/radost-bg-kolekce-detail-1060x570.jpg',
			],
			31 => [
				'description' => $this->translator->translate('collection.sol.description1'),
				'image' => '/www/assets/front/user_content/images/collection/sol/sol-bg-kolekce-detail-1060x570.jpg',
			],
			25 => [
				'description' => $this->translator->translate('collection.odvaznaAKrasna.description1'),
				'image' => '/www/assets/front/user_content/images/collection/odvaznaakrasna/odvazna_krasna-bg-kolekce-detail-1060x570.jpg',
			],
			33 => [
				'description' => $this->translator->translate('collection.tricolor.description1'),
				'image' => '/www/assets/front/user_content/images/collection/tricolor/tricolor-bg-kolekce-detail-1060x570.jpg',
			],
			34 => [
				'description' => $this->translator->translate('collection.tahitskeKralovny.description1'),
				'image' => '/www/assets/front/user_content/images/collection/tahitskekralovny/tahitske-bg-kolekce-detail-1060x570.jpg',
			],
			51 => [
				'description' => $this->translator->translate('collection.toleranceSnubni.description1'),
				'image' => '/www/assets/front/user_content/images/collection/tolerance-snubni/snubni-bg-kolekce-detail-1060x570.jpg',
			],
			35 => [
				'description' => $this->translator->translate('collection.toleranceZasnubni.description1'),
				'image' => '/www/assets/front/user_content/images/collection/tolerance-zasnubni/zasnubni-bg-kolekce-detail-1060x570.jpg',
			],
			50 => [
				'description' => $this->translator->translate('collection.venezia.description1'),
				'image' => '/www/assets/front/user_content/images/collection/venezia/venezia-bg-kolekce-detail-1060x570.jpg',
			],
			37 => [
				'description' => $this->translator->translate('collection.vivaVisionII.description1'),
				'image' => '/www/assets/front/user_content/images/collection/viva/viva-bg-kolekce-detail-1060x570.jpg',
			],
			54 => [
				'description' => $this->translator->translate('collection.voda.description1'),
				'image' => '/www/assets/front/user_content/images/collection/voda/20-0067_JK_web_kolekce_detail_1080x570-voda.jpg',
			],
			20 => [
				'description' => $this->translator->translate('collection.diva.description1'),
				'image' => '/www/assets/front/user_content/images/collection/diva/diva-bg-kolekce-detail-1060x570.jpg',
			],
            49 => [
                'image' => '/www/assets/front/user_content/images/collection/panska/panska-bg-kolekce-detail-1060x570.jpg',
            ],
            76 => [
                'description' => $this->translator->translate('collection.severskeMotivy.description1'),
                'image' => '/www/assets/front/user_content/images/collection/severskemotivy/20-0154_JK_web_kolekce_severska_detail_1080x570.jpg',
            ],
            77 => [
                'description' => $this->translator->translate('collection.kubistik.description1'),
                'image' => '/www/assets/front/user_content/images/collection/kubistik/20-0067_JK_web_kolekce_detail_1080x570-kubistik.jpg',
            ],
		];

		if (!isset($mapping[$category->getId()])) {
			$this->logger->addNotice(sprintf('Missing data for collection with id %d.', $category->getId()), [$category]);
		}

		return $mapping[$category->getId()] ?? [];
	}
}
