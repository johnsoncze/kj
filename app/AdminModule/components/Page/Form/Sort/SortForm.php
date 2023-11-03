<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Page\Form\Sort;

use App\Components\SortForm\SortFormFactory;
use App\Helpers\Entities;
use App\Language\LanguageEntity;
use App\Page\PageEntity;
use App\Page\PageFacadeException;
use App\Page\PageFacadeFactory;
use App\Page\PageRepository;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SortForm extends Control
{


    /** @var Context */
    private $database;

    /** @var LanguageEntity|null */
    private $language;

    /** @var PageFacadeFactory */
    private $pageFacadeFactory;

    /** @var PageRepository */
    private $pageRepo;

    /** @var SortFormFactory */
    private $sortFormFactory;



    public function __construct(Context $context,
                                PageFacadeFactory $pageFacadeFactory,
                                PageRepository $pageRepo,
                                SortFormFactory $sortFormFactory)
    {
        parent::__construct();
        $this->database = $context;
        $this->pageFacadeFactory = $pageFacadeFactory;
        $this->pageRepo = $pageRepo;
        $this->sortFormFactory = $sortFormFactory;
    }



    /**
     * @param $language LanguageEntity
     * @return self
     */
    public function setLanguage(LanguageEntity $language) : self
    {
        $this->language = $language;
        return $this;
    }



    /**
     * @return \App\Components\SortForm\SortForm
     */
    public function createComponentForm() : \App\Components\SortForm\SortForm
    {
    	$parentPrefix = 'parent-';
    	$source = $this->getSource();
    	$pages = strpos($source, $parentPrefix) !== FALSE
			? $this->pageRepo->findPublishedByMoreParentId([str_replace($parentPrefix, '', $source)])
			: $this->pageRepo->findPublishedWithoutParentIdByLanguageIdAndMenuLocation($this->language->getId(), (int)$source);
		$pages = $pages ? Entities::toPair($pages, 'id', 'name') : [];

        $form = $this->sortFormFactory->create();
        $form->setItems($pages);
        $form->setOnSuccess(function (Form $form, array $sorting) {
            $sorting = array_flip($sorting);
            $presenter = $this->getPresenter();

            try {
                $this->database->beginTransaction();
                $pageFacade = $this->pageFacadeFactory->create();
                $pageFacade->saveSort($sorting);
                $this->database->commit();

                $presenter->flashMessage('Řazení bylo uloženo.', 'success');
                $presenter->redirect('this');
            } catch (PageFacadeException $exception) {
                $this->database->rollBack();
                $presenter->flashMessage($exception->getMessage(), 'danger');
            }
        });
        return $form;
    }



    /**
     * @return Form
     */
    public function createComponentMenuForm() : Form
    {
    	$list = [
    		'Dle menu' => PageEntity::getMenuLocationList(),
			'Podstránky pro:' => $this->getPageList(),
		];

        $form = new Form();
        $form->addSelect('source', 'Menu*', $list)
            ->setPrompt('- vyberte -')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyberte menu')
            ->setDefaultValue($this->getSource());
        $form->addSubmit('submit', 'Přejít')
            ->setAttribute('class', 'btn btn-default')
            ->setAttribute('style', 'margin-top:-3px');
        $form->onSuccess[] = function (Form $form) {
        	$values = $form->getValues();
        	$source = $values->source;
            $params['languageId'] = $this->language->getId();
            $params['source'] = $source;
            $this->getPresenter()->redirect('this', $params);
        };
        return $form;
    }



    public function render()
    {
        $this->template->language = $this->language;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @return string
     * @throws \InvalidArgumentException if parameter missing
     */
    private function getSource() : string
    {
        $source = $this->getPresenter()->getParameter('source');
        if ($source === NULL) {
            throw new \InvalidArgumentException('Missing source parameter.');
        }
        return $source;
    }



    /**
	 * @return array
    */
    private function getPageList() : array
	{
		$list = [];
		$pages = $this->pageRepo->findPublishedByLanguageId($this->language->getId());
		foreach ($pages as $page) {
			$key = sprintf('parent-%d', $page->getId());
			$list[$key] = $page->getName();
		}
		return $list;
	}
}