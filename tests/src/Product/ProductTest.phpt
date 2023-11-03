<?php

declare(strict_types = 1);

namespace App\Tests\Product;

require_once __DIR__ . '/../bootstrap.php';

use App\Product\Product;
use App\Tests\BaseTestCase;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductTest extends BaseTestCase
{


	/**
	 * @dataProvider isNewData
	 * @param $time string for example: +1 days
	 * @param $expected bool
	 * @return void
	 */
	public function testIsNew(string $time, bool $expected)
	{
		$product = new Product();
		$product->setNewUntilTo((new \DateTime($time))->format('Y-m-d'));

		Assert::same($expected, $product->isNew());
	}



	/**
	 * @return array
	 */
	public function isNewData() : array
	{
		return [
			['+1 days', TRUE],
			['+1 minutes', TRUE],
			['-1 days', FALSE]
		];
	}
}

(new ProductTest())->run();