<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductParameterSetForm;

use App\AdminModule\Components\ProductVariantForm\ProductTrait;
use App\Helpers\Entities;
use App\Product\Parameter\ParameterStorageException;
use App\Product\Parameter\ParameterStorageFacadeFactory;
use App\ProductParameter\ProductParameterTranslationRepository;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepository;
use App\ProductParameterGroup\Translation\GroupTranslationTrait;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterSetForm extends Control
{


    use GroupTranslationTrait;
    use ProductTrait;

    /** @var Context */
    protected $database;

    /** @var LocalizationResolver */
    private $localizationResolver;

    /** @var ProductParameterGroupTranslationRepository */
    private $groupParameterTranslationRepo;

    /** @var ParameterStorageFacadeFactory */
    private $parameterStorageFacadeFactory;

    /** @var ProductParameterTranslationRepository */
    private $parameterTranslationRepo;



    public function __construct(Context $database,
                                ProductParameterGroupTranslationRepository $groupParameterTranslationRepo,
                                ParameterStorageFacadeFactory $parameterStorageFacadeFactory,
                                ProductParameterTranslationRepository $parameterTranslationRepo)
    {
        parent::__construct();
        $this->database = $database;
        $this->localizationResolver = new LocalizationResolver();
        $this->groupParameterTranslationRepo = $groupParameterTranslationRepo;
        $this->parameterStorageFacadeFactory = $parameterStorageFacadeFactory;
        $this->parameterTranslationRepo = $parameterTranslationRepo;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $groupList = $this->getGroupList($this->groupParameterTranslationRepo, $this->localizationResolver->getDefault());

        $form = new Form();
        $form->addSelect('group', 'Skupina parametrů*', $groupList)
            ->setAttribute('class', 'form-control select2')
            ->setPrompt('- Vyberte -')
            ->setRequired('Vyberte skupinu.');
        $form->addSelect('parameter', 'Parametr*')
            ->setAttribute('class', 'form-control select2')
            ->setPrompt('- Vyberte -');
        $form->addSubmit('submit', 'Přidat')
            ->setAttribute('class', 'btn btn-success');
        $form->onValidate[] = [$this, 'formValidate'];
        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }



    /**
     * Handler for validate form.
     * @param $form Form
     * @return Form
     */
    public function formValidate(Form $form) : Form
    {
        $values = $form->getValues();
        $parameterList = $this->getParameterList($values->group);
        $form['parameter']->setItems($parameterList);
        $values = $form->getValues(); //re-load values, because in the first case the form does not know about the parameter
        if (!$values->parameter) {
            $message = 'Vyberte parametr.';
            $form->addError($message);
            $this->presenter->flashMessage($message, 'danger');
        }
        return $form;
    }



    /**
     * Handler for success form.
     * @param $form Form
     * @return void
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $parameterStorageFacade = $this->parameterStorageFacadeFactory->create();
            $parameterStorageFacade->add((int)$this->getProduct()->getId(), (int)$values->parameter);
            $this->database->commit();

            $presenter->flashMessage('Parameter byl přidán.', 'success');
            $presenter->redirect('this');
        } catch (ParameterStorageException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * Ajax handler for get parameter list.
     * @return void
     */
    public function handleGetParameterList()
    {
        if ($this->presenter->isAjax()) {
            $groupId = (int)$this->getParameter('groupId');
            $parameterList = $this->getParameterList($groupId);
            $parameterList = $parameterList ? array_flip($parameterList) : []; //because jquery sorting the response by id and does not respect sorting from response
            $this->presenter->sendJson($parameterList);
        }
    }



    /**
     * Get parameter list.
     * @param $groupId int
     * @return array
     */
    protected function getParameterList(int $groupId) : array
    {
        $defaultLanguage = $this->localizationResolver->getDefault();
        $parameters = $this->parameterTranslationRepo->findByGroupIdAndLanguageId($groupId, $defaultLanguage->getId());
        return $parameters ? Entities::toPair($parameters, 'productParameterId', 'value') : [];
    }
}