<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Favourite\ProductHeart;

use Nette\Application\UI\Control;
use Nette\Http\Request;
use App\FrontModule\Components\Favourite\FavouriteHelper;



final class ProductHeart extends Control
{
		protected $request;




    public function __construct(Request $request)
    {
				$this->request = $request;
				parent::__construct();
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
    public function renderProductHeart(int $id): void
    {
				$favouriteHelper = new FavouriteHelper();
				$isInFavourite = $favouriteHelper->isInFavourite($this->request, $id);
				
			
        $this->template->setFile(__DIR__ . '/templates/productHeart.latte');
				$this->template->isInFavourite = $isInFavourite;
				$this->template->productId = $id;
        $this->template->render();
    }

		
}