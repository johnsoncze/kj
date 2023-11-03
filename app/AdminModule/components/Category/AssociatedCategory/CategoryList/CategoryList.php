<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\AssociatedCategory\CategoryList;

use App\Category\AssociatedCategory\Category;
use App\Category\AssociatedCategory\CategoryFacadeException;
use App\Category\AssociatedCategory\CategoryFacadeFactory;
use App\Category\AssociatedCategory\CategoryRepository;
use App\Category\CategoryEntity;
use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use Grido\Exception;
use Grido\Grid;
use Nette\Application\AbortException;
use Nette\Application\UI\InvalidLinkException;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CategoryList extends GridoComponent
{


    /** @var CategoryFacadeFactory */
    private $associatedCategoryFacadeFactory;

    /** @var CategoryRepository */
    private $associatedCategoryRepo;

    /** @var CategoryEntity|null */
    private $category;

    /** @var Context */
    private $database;



    public function __construct(CategoryFacadeFactory $categoryFacadeFactory,
                                CategoryRepository $categoryRepository,
                                Context $context,
                                GridoFactory $gridoFactory)
    {
        parent::__construct($gridoFactory);
        $this->associatedCategoryFacadeFactory = $categoryFacadeFactory;
        $this->associatedCategoryRepo = $categoryRepository;
        $this->database = $context;
    }



    /**
     * @param $category CategoryEntity
     */
    public function setCategory(CategoryEntity $category)
    {
        $this->category = $category;
    }



    /**
     * @return Grid
     * @throws InvalidLinkException
     * @throws Exception
     */
    public function createComponentList() : Grid
    {
        $associatedCategoryAnnotation = Category::getAnnotation();
        $associatedCategoryIdAnnotation = $associatedCategoryAnnotation->getPropertyByName('id');

        $categoryAnnotation = CategoryEntity::getAnnotation();
        $categoryIdAnnotation = $categoryAnnotation->getPropertyByName('id');
        $nameAnnotation = $categoryAnnotation->getPropertyByName('name');

        $source = new RepositorySource($this->associatedCategoryRepo);
        $source->setMethodCount('countJoined');
        $source->setRepositoryMethod('findJoined');
        $source->filter([[
            $associatedCategoryAnnotation->getPropertyByName('categoryId')->getColumn()->getName(),
            '=',
            $this->category->getId(),
        ]]);
        $source->setDefaultSort([
            'LENGTH(' . $associatedCategoryIdAnnotation->getColumn()->getName() . ')',
            $associatedCategoryIdAnnotation->getColumn()->getName(),
        ], 'ASC');

        $grido = $this->gridoFactory->create();
        $grido->setModel($source);

        //columns
        $name = $grido->addColumnText($nameAnnotation->getColumn()->getName(), 'Název');
        $name->getHeaderPrototype()->style['width'] = '50%';
        $name->setSortable();

        //actions
        $grido->addActionHref('detail', '', 'Homepage:default')
            ->setCustomHref(function ($row) use ($categoryIdAnnotation) {
                return $this->getPresenter()->link('Category:edit', [
                    'id' => $row[$categoryIdAnnotation->getColumn()->getName()],
                ]);
            })
            ->setIcon('eye');
        $grido->addActionHref('delete', '', 'Homepage:default')
            ->setCustomHref(function ($row) use ($associatedCategoryIdAnnotation) {
                return $this->link('delete!', [
                    'id' => $row[$associatedCategoryIdAnnotation->getColumn()->getName()],
                ]);
            })
            ->setConfirm(function ($row) use ($categoryAnnotation) {
                return sprintf('Opravdu si přejete smazat kategorii \'%s\' ?', $row[$categoryAnnotation->getPropertyByName('name')->getColumn()->getName()]);
            })
            ->setIcon('trash');

        return $grido;
    }



    /**
     * @param $id int
     * @return void
     * @throws AbortException
     */
    public function handleDelete(int $id)
    {
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $categoryFacade = $this->associatedCategoryFacadeFactory->create();
            $categoryFacade->delete($id);
            $this->database->commit();

            $presenter->flashMessage('Kategorie byla smazána.', 'success');
            $presenter->redirect('this');
        } catch (CategoryFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}