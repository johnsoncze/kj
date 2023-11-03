<?php

namespace App\Components\PageArticlesForm;

use App\Article\Module\ModuleRepository;
use App\Components\OgFormContainer\OgFormContainerFactory;
use App\Components\PageBaseForm\PageBaseForm;
use App\Components\SeoFormContainer\SeoFormContainerFactory;
use App\Components\UrlFormContainer\UrlFormContainerFactory;
use App\Helpers\Entities;
use App\Page\PageAddFacadeFactory;
use App\Page\PageEntity;
use App\Page\PageRepositoryFactory;
use App\Page\PageUpdateFacadeFactory;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageArticlesForm extends PageBaseForm
{


    /** @var ModuleRepository */
    protected $articleModuleRepo;

    /** @var string */
    protected $pageType = PageEntity::ARTICLES_TYPE;


    public function __construct(
        PageAddFacadeFactory $pageAddFacadeFactory,
        PageUpdateFacadeFactory $pageUpdateFacadeFactory,
        SeoFormContainerFactory $seoFormContainerFactory,
        UrlFormContainerFactory $urlFormContainerFactory,
        Context $context,
        ModuleRepository $moduleRepository,
        PageRepositoryFactory $pageRepositoryFactory,
        OgFormContainerFactory $ogFormContainerFactory
    ) {
        parent::__construct($pageAddFacadeFactory, $pageUpdateFacadeFactory, $seoFormContainerFactory, $urlFormContainerFactory, $context, $pageRepositoryFactory, $ogFormContainerFactory);
        $this->articleModuleRepo = $moduleRepository;
    }



    /**
     * @param Form $form
     */
    protected function formConfiguration(Form $form)
    {
        $moduleList = $this->articleModuleRepo->findAll();
        $moduleList = $moduleList ? Entities::toPair($moduleList, 'id', 'name') : [];

        $form->addSelect('module', 'Modul článků*')
            ->setPrompt('- vyberte -')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyberte modul.')
            ->setItems($moduleList)
            ->setDefaultValue($this->pageEntity ? $this->pageEntity->getArticleModuleId() : NULL);
    }



    /**
     * @param Form $form
     * @param PageEntity $entity
     * @param bool $newPage
     */
    protected function onSave(Form $form, PageEntity $entity, bool $newPage)
    {
        parent::onSave($form, $entity, $newPage);

        $values = $form->getValues();

        $updateFacade = $this->pageUpdateFacadeFactory->create();
        $updateFacade->updateFromParameters($entity->getId(), $values->module);
    }
}