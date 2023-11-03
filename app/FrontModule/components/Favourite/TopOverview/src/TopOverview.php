<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Favourite\TopOverview;

use Nette\Application\UI\Control;
use Nette\Http\Request;
use App\FrontModule\Components\Favourite\FavouriteHelper;



final class TopOverview extends Control
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
    public function renderTopOverview(): void
    {
				$favouriteHelper = new FavouriteHelper();
				$favouriteProducts = $favouriteHelper->GetFavouriteProducts($this->request);
				
			
        $this->template->setFile(__DIR__ . '/templates/overview.latte');
				$this->template->favouriteCount = count($favouriteProducts);
        $this->template->render();
    }

		
}