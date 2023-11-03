<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Product;

use App\Product\Production\Time\Time;
use App\ShoppingCart\Product\ShoppingCartProduct;
use App\Tests\BaseTestCase;
use App\Tests\Product\Production\Time\TimeTestTrait;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartProductTest extends BaseTestCase
{


	use TimeTestTrait;



	/**
	 * @dataProvider getUnitPriceDataLoop
	 *
	 * @param $price float
	 * @param $discount float
	 * @param $quantity int
	 * @param $expectedPrice float
	 */
	public function testGetUnitPrice(float $price, float $discount, int $quantity, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setQuantity($quantity);
		$product->setDiscount($discount);
		$product->setPrice($price);

		Assert::same($expectedPrice, $product->getUnitPrice());
	}



	/**
	 * @return array
	 */
	public function getUnitPriceDataLoop() : array
	{
		return [
			[150.50, 0.0, 2, 150.50],
			[2554.70, 17.0, 2, 2120.401],
		];
	}



	/**
	 * @dataProvider getGetUnitPriceWithSurchargeDataLoop
	 *
	 * @param $price float
	 * @param $discount float
	 * @param $quantity int
	 * @param $time Time|null
	 * @param $expectedPrice float
	 */
	public function testGetUnitPriceWithSurcharge(float $price, float $discount, int $quantity, Time $time = NULL, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setPrice($price);
		$product->setDiscount($discount);
		$product->setQuantity($quantity);
		$time ? $product->setProductionTime($time) : NULL;

		Assert::same($expectedPrice, $product->getUnitPriceWithSurcharge());
	}



	/**
	 * @return array
	 */
	public function getGetUnitPriceWithSurchargeDataLoop() : array
	{
		return [
			[350.20, 15.0, 5, $this->createTestTime(), 446.505],
			[350.20, 0.0, 5, $this->createTestTime(), 525.3],
			[350.20, 10.0, 5, NULL, 315.18],
			[350.20, 0.0, 5, NULL, 350.20],
		];
	}



	/**
	 * @dataProvider getUnitPriceBeforeDiscountWithSurcharge
	 *
	 * @param $price float
	 * @param $discount float
	 * @param $quantity int
	 * @param $time Time|null
	 * @param $expectedPrice float
	 */
	public function testGetUnitPriceBeforeDiscountWithSurcharge(float $price, float $discount, int $quantity, Time $time = NULL, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setPrice($price);
		$product->setDiscount($discount);
		$product->setQuantity($quantity);
		$time ? $product->setProductionTime($time) : NULL;

		Assert::same($expectedPrice, $product->getUnitPriceBeforeDiscountWithSurcharge());
	}



	/**
	 * @return array
	 */
	public function getUnitPriceBeforeDiscountWithSurcharge() : array
	{
		return [
			[350.20, 15.0, 5, $this->createTestTime(), 525.3],
			[350.20, 0.0, 5, $this->createTestTime(), 525.3],
			[350.20, 10.0, 5, NULL, 350.20],
			[350.20, 0.0, 5, NULL, 350.20],
		];
	}



	/**
	 * @dataProvider getUnitPriceWithoutVatDataLoop
	 *
	 * @param $price float
	 * @param $discount float
	 * @param $vat float
	 * @param $quantity int
	 * @param $expectedPrice float
	 */
	public function testGetUnitPriceWithoutVat(float $price, float $discount, float $vat, int $quantity, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setPrice($price);
		$product->setDiscount($discount);
		$product->setVat($vat);
		$product->setQuantity($quantity);

		Assert::same($expectedPrice, $product->getUnitPriceWithoutVat());
	}



	/**
	 * @return array
	 */
	public function getUnitPriceWithoutVatDataLoop() : array
	{
		return [
			[350.20, 15.0, 21.0, 5, 246.0083],
			[350.20, 0.0, 21.0, 5, 289.4215],
			[350.20, 0.0, 21.0, 1, 289.4215],
			[350.20, 15.0, 21.0, 5, 246.0083],
		];
	}



	/**
	 * @dataProvider getUnitPriceBeforeDiscountDataLoop
	 *
	 * @param $price float
	 * @param $discount float
	 * @param $quantity int
	 * @param $expectedPrice float
	 */
	public function testGetUnitPriceBeforeDiscount(float $price, float $discount, int $quantity, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setPrice($price);
		$product->setDiscount($discount);
		$product->setQuantity($quantity);

		Assert::same($expectedPrice, $product->getUnitPriceBeforeDiscount());
	}



	/**
	 * @return array
	 */
	public function getUnitPriceBeforeDiscountDataLoop() : array
	{
		return [
			[350.20, 15.0, 5, 350.20],
			[350.20, 0.0, 5, 350.20],
			[350.20, 15.0, 1, 350.20],
			[350.20, 0.0, 1, 350.20],
		];
	}



	/**
	 * @dataProvider getUnitPriceBeforeDiscountWithoutVatDataLoop
	 *
	 * @param $price float
	 * @param $discount float
	 * @param $vat float
	 * @param $quantity int
	 * @param $expectedPrice float
	 */
	public function testGetUnitPriceBeforeDiscountWithoutVat(float $price, float $discount, float $vat, int $quantity, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setPrice($price);
		$product->setDiscount($discount);
		$product->setVat($vat);
		$product->setQuantity($quantity);

		Assert::same($expectedPrice, $product->getUnitPriceBeforeDiscountWithoutVat());
	}



	/**
	 * @return array
	 */
	public function getUnitPriceBeforeDiscountWithoutVatDataLoop() : array
	{
		return [
			[350.20, 15.0, 15.0, 5, 304.5217],
			[350.20, 0.0, 15.0, 5, 304.5217],
			[350.20, 15.0, 15.0, 1, 304.5217],
			[350.20, 0.0, 15.0, 1, 304.5217],
		];
	}



	/**
	 * @dataProvider getSummaryPriceDataLoop
	 *
	 * @param $price float
	 * @param $discount float
	 * @param $quantity int
	 * @param $time Time
	 * @param $expectedPrice float
	 */
	public function testGetSummaryPrice(float $price, float $discount, int $quantity, Time $time = NULL, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setPrice($price);
		$product->setDiscount($discount);
		$time ? $product->setProductionTime($time) : NULL;
		$product->setQuantity($quantity);

		Assert::same($expectedPrice, $product->getSummaryPrice());
	}



	/**
	 * @return array
	 */
	public function getSummaryPriceDataLoop() : array
	{
		$time = new Time();
		$time->setSurcharge(50.0);
		$time->setState(Time::PUBLISH);

		return [
			[350.20, 15.0, 5, $time, 2232.525],
			[350.20, 15.0, 4, NULL, 1190.68],
			[350.20, 0.0, 5, $time, 2626.5],
			[350.20, 0.0, 5, NULL, 1751.0],
		];
	}



	/**
	 * @dataProvider getSummaryPriceWithoutVatDataLoop
	 *
	 * @param $price float
	 * @param $vat float
	 * @param $discount float
	 * @param $quantity int
	 * @param $time Time|null
	 */
	public function testGetSummaryPriceWithoutVat(float $price, float $vat, float $discount, int $quantity, Time $time = NULL, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setPrice($price);
		$product->setVat($vat);
		$product->setDiscount($discount);
		$product->setQuantity($quantity);
		$time ? $product->setProductionTime($time) : NULL;

		Assert::same($expectedPrice, $product->getSummaryPriceWithoutVat());
	}



	/**
	 * @return array
	 */
	public function getSummaryPriceWithoutVatDataLoop() : array
	{
		$time = new Time();
		$time->setSurcharge(50.0);
		$time->setState(Time::PUBLISH);

		return [
			[350.20, 15.0, 15.0, 5, $time, 1941.3261],
			[350.20, 15.0, 15.0, 5, NULL, 1294.2174],
			[350.20, 15.0, 0.0, 5, $time, 2283.9130],
			[350.20, 15.0, 0.0, 5, NULL, 1522.6087],
		];
	}



	/**
	 * @dataProvider getSummaryPriceBeforeDiscountDataLoop
	 *
	 * @param $price float
	 * @param $discount float
	 * @param $quantity int
	 * @param $time Time|null
	 * @param $expectedPrice float
	 */
	public function testGetSummaryPriceBeforeDiscount(float $price, float $discount, int $quantity, Time $time = NULL, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setPrice($price);
		$product->setDiscount($discount);
		$product->setQuantity($quantity);
		$time ? $product->setProductionTime($time) : NULL;

		Assert::same($expectedPrice, $product->getSummaryPriceBeforeDiscount());
	}



	/**
	 * @return array
	 */
	public function getSummaryPriceBeforeDiscountDataLoop() : array
	{
		return [
			[350.20, 15.0, 5, $this->createTestTime(), 2626.5],
			[350.20, 0.0, 5, $this->createTestTime(), 2626.5],
			[350.20, 15.0, 5, NULL, 1751],
			[350.20, 0.0, 5, NULL, 1751],
		];
	}



	/**
	 * @dataProvider getSummaryPriceBeforeDiscountWithoutVatDataLoop
	 *
	 * @param $price float
	 * @param $vat float
	 * @param $discount float
	 * @param $quantity int
	 * @param $time Time|null
	 * @param $expectedPrice float
	 */
	public function testGetSummaryPriceBeforeDiscountWithoutVat(float $price, float $vat, float $discount, int $quantity, Time $time = NULL, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setPrice($price);
		$product->setVat($vat);
		$product->setDiscount($discount);
		$product->setQuantity($quantity);
		$time ? $product->setProductionTime($time) : NULL;

		Assert::same($expectedPrice, $product->getSummaryPriceBeforeDiscountWithoutVat());
	}



	/**
	 * @return array
	 */
	public function getSummaryPriceBeforeDiscountWithoutVatDataLoop() : array
	{
		return [
			[350.20, 15.0, 15.0, 5, $this->createTestTime(), 2283.9130],
			[350.20, 15.0, 0.0, 5, $this->createTestTime(), 2283.9130],
			[350.20, 15.0, 15.0, 5, NULL, 1522.6087],
			[350.20, 15.0, 0.0, 5, NULL, 1522.6087],
		];
	}



	/**
	 * @dataProvider getSurchargeDataLoop
	 *
	 * @param $price float
	 * @param $discount float
	 * @param $quantity int
	 * @param $time Time|null
	 * @param $expectedPrice float
	 */
	public function testGetSurcharge(float $price, float $discount, int $quantity, Time $time = NULL, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setPrice($price);
		$product->setDiscount($discount);
		$product->setQuantity($quantity);
		$time ? $product->setProductionTime($time) : NULL;

		Assert::same($expectedPrice, $product->getSurcharge());
	}



	/**
	 * @return array
	 */
	public function getSurchargeDataLoop() : array
	{
		return [
			[350.20, 15.0, 5, $this->createTestTime(), 744.175],
			[350.20, 0.0, 5, $this->createTestTime(), 875.5],
			[350.20, 15.0, 5, NULL, 0.0],
			[350.20, 0.0, 5, NULL, 0.0],
		];
	}



	/**
	 * @dataProvider getSurchargeBeforeDiscountDataLoop
	 *
	 * @param $price float
	 * @param $discount float
	 * @param $quantity int
	 * @param $time Time|null
	 * @param $expectedPrice float
	 */
	public function testGetSurchargeBeforeDiscount(float $price, float $discount, int $quantity, Time $time = NULL, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setPrice($price);
		$product->setDiscount($discount);
		$product->setQuantity($quantity);
		$time ? $product->setProductionTime($time) : NULL;

		Assert::same($expectedPrice, $product->getSurchargeBeforeDiscount());
	}



	/**
	 * @return array
	 */
	public function getSurchargeBeforeDiscountDataLoop() : array
	{
		return [
			[350.20, 15.0, 5, $this->createTestTime(), 875.5],
			[350.20, 0.0, 5, $this->createTestTime(), 875.5],
			[350.20, 15.0, 5, NULL, 0.0],
			[350.20, 0.0, 5, NULL, 0.0],
		];
	}



	/**
	 * @dataProvider getSurchargeWithoutVatDataLoop
	 *
	 * @param $price float
	 * @param $vat float
	 * @param $discount float
	 * @param $quantity int
	 * @param $time Time|null
	 * @param $expectedPrice float
	 */
	public function testGetSurchargeWithoutVat(float $price, float $vat, float $discount, int $quantity, Time $time = NULL, float $expectedPrice)
	{
		$product = new ShoppingCartProduct();
		$product->setPrice($price);
		$product->setVat($vat);
		$product->setDiscount($discount);
		$product->setQuantity($quantity);
		$time ? $product->setProductionTime($time) : NULL;

		Assert::same($expectedPrice, $product->getSurchargeWithoutVat());
	}



	/**
	 * @return array
	 */
	public function getSurchargeWithoutVatDataLoop() : array
	{
		return [
			[350.20, 21.0, 15.0, 5, $this->createTestTime(), 615.0207],
			[350.20, 21.0, 21.0, 1, $this->createTestTime(), 114.3215],
			[350.20, 15.0, 0.0, 5, $this->createTestTime(), 761.3043],
			[350.20, 21.0, 15.0, 5, NULL, 0.0],
			[350.20, 21.0, 15.0, 5, NULL, 0.0],
		];
	}



	/**
	 * @dataProvider getSurchargePercentDataLoop
	 *
	 * @param $time Time|null
	 * @param $expectedPercent float
	 */
	public function testGetSurchargePercent(Time $time = NULL, float $expectedPercent)
	{
		$product = new ShoppingCartProduct();
		$time ? $product->setProductionTime($time) : NULL;

		Assert::same($expectedPercent, $product->getSurchargePercent());
	}



	/**
	 * @return array
	 */
	public function getSurchargePercentDataLoop() : array
	{
		return [
			[$this->createTestTime(), 50.0],
			[NULL, 0.0],
		];
	}

}

(new ShoppingCartProductTest())->run();