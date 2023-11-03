<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Catalog\Form;

use App\Catalog\Catalog;
use App\Catalog\CatalogFacadeException;
use App\Catalog\CatalogFacadeFactory;
use App\Catalog\Translation\CatalogTranslationFacadeException;
use App\Catalog\Translation\CatalogTranslationFacadeFactory;
use App\Components\TranslationFormTrait;
use App\Helpers\Arrays;
use App\Helpers\Summernote;
use App\Libs\FileManager\Responses\DeleteFileResponse;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CatalogForm extends Control
{


    use TranslationFormTrait;

    /** @var Catalog|null */
    private $catalog;

    /** @var CatalogFacadeFactory */
    private $catalogFacadeFactory;

    /** @var CatalogTranslationFacadeFactory */
    private $catalogTranslationFacadeFactory;

    /** @var Context */
    private $database;

    /** @var string|null */
    private $type;



    public function __construct(CatalogFacadeFactory $catalogFacadeFactory,
                                CatalogTranslationFacadeFactory $catalogTranslationFacadeFactory,
                                Context $context)
    {
        parent::__construct();
        $this->catalogFacadeFactory = $catalogFacadeFactory;
        $this->catalogTranslationFacadeFactory = $catalogTranslationFacadeFactory;
        $this->database = $context;
    }



    /**
     * @param $catalog Catalog
     * @return self
     */
    public function setCatalog(Catalog $catalog) : self
    {
        $this->catalog = $catalog;
        return $this;
    }



    /**
     * @param $type string
     * @return self
     */
    public function setType(string $type) : self
    {
        $this->type = $type;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $stateList = Arrays::toPair(Catalog::getStates(), 'key', 'translation');

        $form = new Form();
        $form->addSelect('state', 'Stav', $stateList)
            ->setAttribute('class', 'form-control');
        $form->addText('title')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyplňte toto pole.')
            ->setMaxLength(255);
        $form->addText('subtitle')
            ->setAttribute('class', 'form-control')
            ->setMaxLength(100);
        $form->addUpload('photo');
        $form->addTextArea('text')
            ->setAttribute('class', 'form-control')
            ->setMaxLength(5000)
            ->setEmptyValue(Summernote::EMPTY_STRING_VALUE)
			->setHtmlId('ckEditor');
        $form->addUpload('file');
        $form->addTextArea('about')
            ->setAttribute('class', 'form-control')
            ->setMaxLength(5000)
            ->setHtmlId('ckEditor2');
        $form->addSubmit('submit', 'Uložit')
            ->setAttribute('class', 'btn btn-success');
        $form->onSuccess[] = [$this, 'formSuccess'];
        $this->setDefaultFormValues($form);

        return $form;
    }



    /**
     * @param $form Form
     * @return void
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $presenter = $this->getPresenter();
        $catalogId = $this->catalog ? $this->catalog->getId() : NULL;
        $catalogTranslationId = $this->catalog ? $this->catalog->getTranslation()->getId() : NULL;
        $languageId = $this->getLocale()->getId();

        try {
            $this->database->beginTransaction();

            //save catalog
            $catalogFacade = $this->catalogFacadeFactory->create();
            $catalog = $catalogFacade->save($catalogId, $this->type, $values->state);
            $values->photo->hasFile() ? $catalogFacade->uploadPhoto($catalog->getId(), $values->photo) : NULL;

            //save catalog translation
            $catalogTranslationFacade = $this->catalogTranslationFacadeFactory->create();
            $catalogTranslation = $catalogTranslationFacade->save($catalogTranslationId, $catalog->getId(), $languageId, $values->title, $values->subtitle ?: null, $values->text ?: null, $values->about ?: null);
            $values->file->hasFile() ? $catalogTranslationFacade->uploadFile($catalogTranslation->getId(), $values->file) : NULL;

            $this->database->commit();

            $presenter->flashMessage('Formulář byl uložen.', 'success');
            $presenter->redirect('Catalog:edit', ['id' => $catalog->getId()]);
        } catch (CatalogFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        } catch (CatalogTranslationFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    public function render()
    {
        $this->template->catalog = $this->catalog;
        $this->template->locale = (new LocalizationResolver())->getActual();

        $file = __DIR__ . '/templates/' . $this->type . '.latte';
        $this->template->setFile($file);
        $this->template->render();
    }



    /**
     * @param $id int id of catalog
     * @return void
     */
    public function handleDeletePhoto(int $id)
    {
        $presenter = $this->getPresenter();

        if ($presenter->isAjax() === TRUE) {
            try {
                $this->database->beginTransaction();
                $catalogFacade = $this->catalogFacadeFactory->create();
                $catalogFacade->deletePhoto($id);
                $this->database->commit();
                $response = new DeleteFileResponse('Fotografie byla smazána.', DeleteFileResponse::SUCCESS);
            } catch (CatalogFacadeException $exception) {
                $this->database->rollBack();
                $response = new DeleteFileResponse($exception->getMessage(), DeleteFileResponse::ERROR);
            }
            $presenter->sendJson($response->getResponseArray());
        }
    }



    /**
     * @param $id int id of catalog translation
     * @return void
     */
    public function handleDeleteFile(int $id)
    {
        $presenter = $this->getPresenter();

        if ($presenter->isAjax() === TRUE) {
            try {
                $this->database->beginTransaction();
                $catalogTranslationFacade = $this->catalogTranslationFacadeFactory->create();
                $catalogTranslationFacade->deleteFile($id);
                $this->database->commit();
                $response = new DeleteFileResponse('Fotografie byla smazána.', DeleteFileResponse::SUCCESS);
            } catch (CatalogTranslationFacadeException $exception) {
                $this->database->rollBack();
                $response = new DeleteFileResponse($exception->getMessage(), DeleteFileResponse::ERROR);
            }
            $presenter->sendJson($response->getResponseArray());
        }
    }



    /**
     * @param $form Form
     * @return Form
     */
    private function setDefaultFormValues(Form $form) : Form
    {
        if ($this->catalog instanceof Catalog) {
            $translation = $this->catalog->getTranslation();
            $form->setDefaults([
                'state' => $this->catalog->getState(),
                'title' => $translation->getTitle(),
                'subtitle' => $translation->getSubtitle(),
                'text' => $translation->getText(),
                'about' => $translation->getAbout(),
            ]);
        }
        return $form;
    }
}