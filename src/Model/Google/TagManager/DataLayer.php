<?php

declare(strict_types = 1);

namespace App\Google\TagManager;

use Nette\Http\Session;
use Nette\Http\SessionSection;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class DataLayer
{


	/** @var int data count */
	protected $iterator = 0;

	/** @var SessionSection */
	protected $session;



	public function __construct(Session $session)
	{
		$this->session = $session->getSection('gtmDataLayer');
	}



	/**
	 * @param $data array
	 * @return self
	 */
	public function add(array $data) : self
	{
		$this->session[$this->iterator] = $data;
		$this->iterator++;
		return $this;
	}



	/**
	 * @return array
	*/
	public function getData() : array
	{
		return (array)$this->session->getIterator();
	}



	/**
	 * Remove all data.
	 * @return void
	 */
	public function removeData()
	{
		$this->session->remove();
		$this->resetIterator();
	}



	/**
	 * @return void
	*/
	protected function resetIterator()
	{
		$this->iterator = 0;
	}
}