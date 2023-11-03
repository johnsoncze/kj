<?php

declare(strict_types = 1);

namespace App\ProductParameter\Helper;

use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class HelperRepository extends BaseRepository
{


	/** @var string */
	protected $entityName = Helper::class;



	/**
	 * @param $key string
	 * @return Helper[]|array
	 */
	public function findByKey(string $key) : array
	{
		$filter['where'][] = ['key', '=', $key];
		$filter['sort'] = ['name', 'ASC'];
		return $this->findBy($filter) ?: [];
	}
}