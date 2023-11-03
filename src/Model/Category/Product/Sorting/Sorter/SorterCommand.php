<?php

declare(strict_types = 1);

namespace App\Category\Product\Sorting\Sorter;

use App\BaseCommand;
use App\Category\CategoryEntity;
use App\Category\CategoryRepository;
use Nette\DI\MissingServiceException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SorterCommand extends BaseCommand
{


	/** @var CategoryRepository|null */
	private $categoryRepo;



	/**
	 * @inheritdoc
	 */
	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		parent::initialize($input, $output);
		$this->categoryRepo = $this->container->getByType(CategoryRepository::class);
	}



	/**
	 * @inheritdoc
	 */
	protected function configure()
	{
		parent::configure();
		$this->setName('category:product:sort')
			->setDescription('Sort products in categories.');
	}



	/**
	 * @inheritdoc
	 * @throws MissingServiceException
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		Debugger::timer();
		$categorySorted = 0;
		$sorterList = CategoryEntity::getProductSorters();
		$categories = $this->categoryRepo->findWithProductSorter();

		$this->writeInfoMessage($output, sprintf('Řazení produktů v kategoriích bylo spuštěno. Celkem kategorií pro řazení: %d', count($categories)));

		foreach ($categories as $category) {
			$sorterId = $category->getProductSorter();
			$sorter = $sorterList[$sorterId] ?? NULL;
			if ($sorter === NULL) {
				$this->writeErrorMessage($output, sprintf('Chybí řadící algoritmus s id \'%d\' pro kategorii \'%s\' s id \'%d\'.', $sorterId, $category->getName(), $category->getId()));
				continue;
			}

			/** @var $sorterClass ISorter */
			$sorterClass = $this->container->getByType($sorter['class']);
			$sorterClass->execute($category);
			$categorySorted++;
		}

		$this->writeInfoMessage($output, sprintf('Řazení produktů v kategoriích bylo dokončeno v čase \'%s\' sekund. Celkem seřazeno kategorií: %d.', Debugger::timer(), $categorySorted));
		return 0;
	}
}