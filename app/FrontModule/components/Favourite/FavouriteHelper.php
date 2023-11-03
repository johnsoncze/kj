<?php

declare(strict_types=1);

namespace App\FrontModule\Components\Favourite;


use Nette\Http\Response;
use Nette\Http\Request;
use Nette\Application\UI\Control;


class FavouriteHelper extends Control
{
	public function __construct()
	{
	}

	private function SaveFavouriteProducts(Response $response, array $favouriteProducts): void
	{
			$response->setCookie("favouriteItems", implode(",", $favouriteProducts),"365 days");
	}

	
	public function GetFavouriteProducts(Request $request): array
	{
			$favouriteProducts = $request->getCookie("favouriteItems");
			return isset($favouriteProducts) ? explode(",", $favouriteProducts) : array();
	}

	
	public function AddFavouriteProduct(Request $request, Response $response, int $id): array
	{
				$favouriteProducts = $this->GetFavouriteProducts($request);
				if (!in_array($id, $favouriteProducts)) {
						$favouriteProducts[] = $id;				
				}

				$this->SaveFavouriteProducts($response, $favouriteProducts);
				
				return $favouriteProducts;
	}
		
	
	public function RemoveFavouriteProduct(Request $request, Response $response, int $id): array
	{
				$favouriteProducts = $this->GetFavouriteProducts($request);
				if (($key = array_search($id, $favouriteProducts)) !== false) {
						unset($favouriteProducts[$key]);
				}				

				$this->SaveFavouriteProducts($response, $favouriteProducts);
				
				return $favouriteProducts;
	}
	
	
	public function isInFavourite(Request $request, int $id): bool
	{
				$favouriteProducts = $this->GetFavouriteProducts($request);
				if (($key = array_search($id, $favouriteProducts)) !== false) {
						return true;
				}
				
				return false;
	}	

}