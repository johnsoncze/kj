<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\Block\Timeline\Year;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Item
{


	/** @var string */
	protected $title;

	/** @var string|null */
	protected $titleClass;

	/** @var string */
	protected $year;

	/** @var string */
	protected $description;

	/** @var string */
	protected $images;

	/** @var bool */
	protected $reverse;



	public function __construct(string $title,
								string $year,
								string $description,
								string $images,
								bool $reverse = FALSE)
	{
		$this->title = $title;
		$this->year = $year;
		$this->description = $description;
		$this->images = $images;
		$this->reverse = $reverse;
	}



	/**
	 * @return string
	 */
	public function getTitle() : string
	{
		return $this->title;
	}



	/**
	 * @param $class string
	 * @return self
	*/
	public function setTitleClass(string $class) : self
	{
		$this->titleClass = $class;
		return $this;
	}



	/**
	 * @return string|null
	*/
	public function getTitleClass()
	{
		return $this->titleClass;
	}



	/**
	 * @return string
	 */
	public function getYear() : string
	{
		return $this->year;
	}



	/**
	 * @return string
	 */
	public function getDescription() : string
	{
		return $this->description;
	}



	/**
	 * @return string
	 */
	public function getImages() : string
	{
		return $this->images;
	}



	/**
	 * @return bool
	 */
	public function isReverse() : bool
	{
		return $this->reverse;
	}


}