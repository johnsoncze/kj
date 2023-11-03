<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Catalog\CatalogList\CatalogList;
use App\AdminModule\Components\Catalog\CatalogList\CatalogListFactory;
use App\AdminModule\Components\Catalog\Form\CatalogForm;
use App\AdminModule\Components\Catalog\Form\CatalogFormFactory;
use App\AdminModule\Components\Catalog\SortForm\SortForm;
use App\AdminModule\Components\Catalog\SortForm\SortFormFactory;
use App\Catalog\Catalog;
use App\Catalog\CatalogRepository;
use App\NotFoundException;
use Nette\Application\BadRequestException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CatalogPresenter extends AdminModulePresenter
{


    /** @var Catalog|null */
    public $catalog;

    /** @var CatalogFormFactory @inject */
    public $catalogFormFactory;

    /** @var CatalogListFactory @inject */
    public $catalogListFactory;

    /** @var CatalogRepository @inject */
    public $catalogRepo;

    /** @var SortFormFactory @inject */
    public $catalogSortFormFactory;

    /** @var string @persistent */
    public $type = Catalog::TYPE_REPRESENTATIVE_CATALOG;

    /** @var array */
    private $typeValues = [];



    public function startup()
    {
        parent::startup();

        //validation of catalog type
        $types = Catalog::getTypes();
        $type = $types[$this->type] ?? NULL;
        if ($type === NULL) {
            throw new BadRequestException(NULL, 404);
        }
        $this->typeValues = $type;
    }



    /**
     * @inheritdoc
     */
    public function beforeRender()
    {
        parent::beforeRender();
        $this->_navigation->addLink($this->link('Catalog:default', ['type' => $this->type]), $this->typeValues['translation'], 1);

        $this->template->type = $this->type;
    }



    /**
     * @param $id int
     * @throws BadRequestException
     */
    public function actionEdit(int $id)
    {
        try {
            $this->catalog = $this->catalogRepo->getOneById($id);
            $this->template->setFile(__DIR__ . '/templates/Catalog/add.latte');
        } catch (NotFoundException $exception) {
            throw new BadRequestException(NULL, 404);
        }
    }



    /**
     * @return CatalogList
     */
    public function createComponentCatalogList() : CatalogList
    {
        $list = $this->catalogListFactory->create();
        $list->setType($this->type);
        return $list;
    }



    /**
     * @return CatalogForm
     */
    public function createComponentCatalogForm() : CatalogForm
    {
        $form = $this->catalogFormFactory->create();
        $form->setType($this->type);
        $this->catalog !== NULL ? $form->setCatalog($this->catalog) : NULL;
        return $form;
    }



    /**
     * @return SortForm
    */
    public function createComponentCatalogSortForm() : SortForm
    {
        $form = $this->catalogSortFormFactory->create();
        $form->setType($this->type);
        return $form;
    }
}