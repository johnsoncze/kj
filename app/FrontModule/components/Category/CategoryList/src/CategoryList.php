<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\CategoryList;

use App\Category\CategoryEntity;
use App\Category\CategoryRepository;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CategoryList extends Control
{


    /** @var CategoryRepository */
    private $categoryRepo;

    /** @var CategoryEntity|null */
    private $parentCategory;



    public function __construct(CategoryRepository $categoryRepo)
    {
        parent::__construct();
        $this->categoryRepo = $categoryRepo;
    }



    /**
     * @param $category CategoryEntity
     * @return self
     */
    public function setParentCategory(CategoryEntity $category) : self
    {
        $this->parentCategory = $category;
        return $this;
    }



    public function render()
    {
        $this->template->categories = $this->getCategories();
        $this->template->setFile(__DIR__ . '/templates/default.latte');
        $this->template->render();
    }



    public function renderCollection()
    {
        $this->template->categories = $this->getCategories();
        $this->template->setFile(__DIR__ . '/templates/collection.latte');
        $this->template->render();
    }



    public function renderBlock()
	{
		$this->template->parentCategory = $this->parentCategory;
		$this->template->categories = $this->getCategories();
		$this->template->setFile(__DIR__ . '/templates/block.latte');
		$this->template->render();
	}



    /**
     * @return CategoryEntity[]|array
     */
    private function getCategories() : array
    {
        return $this->categoryRepo->findPublishedByParentId($this->parentCategory->getId());
    }



    /**
	 * @param $category CategoryEntity
	 * @return string|null
    */
    public function getCollectionProductPhoto(CategoryEntity $category)
	{
		$products = [
			15 => '/www/assets/front/user_content/images/collection/andele/andel-300x470.png',
      17 => '/www/assets/front/user_content/images/collection/classic/classic-300x470.png',
      18 => '/www/assets/front/user_content/images/collection/denni/denni-bg-kolekce-detail-300x460.png',
			19 => '/www/assets/front/user_content/images/collection/detska/detska-300x470.png',
			20 => '/www/assets/front/user_content/images/collection/diva/diva-300x470.png',
			22 => '/www/assets/front/user_content/images/collection/kvetiny/kvetiny-300x470.png',
			23 => '/www/assets/front/user_content/images/collection/laskaviranadeje/laskaviranadeje-300x470.png',
			24 => '/www/assets/front/user_content/images/collection/motyli/motyli-300x470.png',
			25 => '/www/assets/front/user_content/images/collection/odvaznaakrasna/odvazna-krasna-300x470.png',
			26 => '/www/assets/front/user_content/images/collection/organickemotivy/organicke-300x470.png',
      27 => '/www/assets/front/user_content/images/collection/perly/perly-300x470.png',
			28 => '/www/assets/front/user_content/images/collection/primavera/primavera-300x470.png',
      29 => '/www/assets/front/user_content/images/collection/radost/radost-300x470.png',
			31 => '/www/assets/front/user_content/images/collection/sol/sol-300x470.png',
			33 => '/www/assets/front/user_content/images/collection/tricolor/tricolor-300x470.png',
			34 => '/www/assets/front/user_content/images/collection/tahitskekralovny/tahitske-300x470.png',
			35 => '/www/assets/front/user_content/images/collection/tolerance-zasnubni/zasnubni-bg-kolekce-detail-300x460.png',
      37 => '/www/assets/front/user_content/images/collection/viva/viva-300x470.png',
      49 => '/www/assets/front/user_content/images/collection/panska/panska-300x470.png',
			50 => '/www/assets/front/user_content/images/collection/venezia/venezia-300x470.png',
			51 => '/www/assets/front/user_content/images/collection/tolerance-snubni/snubni-300x470-2.png',
			54 => '/www/assets/front/user_content/images/collection/voda/voda-300x470.png',
			76 => '/www/assets/front/user_content/images/collection/severskemotivy/severskemotivy-300x470.png',
            77 => '/www/assets/front/user_content/images/collection/kubistik/kubistik-300x470b.png',
		];
		return $products[$category->getId()] ?? NULL;
	}
}
