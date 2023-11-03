<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductFiltrationForm;

use App\Helpers\Entities;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterTranslationEntity;
use App\ProductParameter\ProductParameterTranslationRepository;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepository;
use App\ProductParameterGroup\Translation\GroupTranslationTrait;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductFiltrationForm extends Control
{


    use GroupTranslationTrait;


    /** @var LocalizationResolver */
    private $localizationResolver;

    /** @var ProductParameterGroupRepository @inject */
    private $groupParameterRepo;

    /** @var ProductParameterGroupTranslationRepository */
    private $groupParameterTranslationRepo;

    /** @var ProductParameterTranslationRepository */
    private $parameterTranslationRepo;

    /** @var ProductParameterTranslationEntity[]|array */
    private $parameters = [];



    public function __construct(ProductParameterGroupRepository $productParameterGroupRepository,
                                ProductParameterGroupTranslationRepository $groupParameterTranslationRepo,
                                ProductParameterTranslationRepository $parameterTranslationRepo)
    {
        parent::__construct();
        $this->localizationResolver = new LocalizationResolver();
        $this->groupParameterRepo = $productParameterGroupRepository;
        $this->groupParameterTranslationRepo = $groupParameterTranslationRepo;
        $this->parameterTranslationRepo = $parameterTranslationRepo;
    }



    /**
     * Add parameter which is set.
     * @param $parameter ProductParameterEntity
     * @return self
     */
    public function addParameter(ProductParameterEntity $parameter) : self
    {
        if (!isset($this->parameters[$parameter->getProductParameterGroupId()][$parameter->getId()])) {
            $this->parameters[$parameter->getProductParameterGroupId()][$parameter->getId()] = $parameter;
        }
        return $this;
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
        $form->addSubmit('submit', 'Nastavit')
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
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $presenter = $this->getPresenter();
        $parameterId = $presenter->getParameter('parameter', []);

        //array_merge because some parameter can be set already
        $presenter->redirect('this', ['parameter' => array_merge([$values->parameter], $parameterId)]);
    }



    public function render()
    {
        $this->template->groups = $this->getGroupsBySetParameters();
        $this->template->parameters = $this->parameters;

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
     * @param $parameterId int
     * @return void
     * @throws AbortException
     */
    public function handleRemoveParameter(int $parameterId)
    {
        $presenter = $this->getPresenter();
        $parameters = $presenter->getParameters();
        foreach ($parameters['parameter'] ?? [] as $key => $parameter) {
            if ((int)$parameter === $parameterId) {
                unset($parameters['parameter'][$key]);
                break;
            }
        }
        unset($parameters['do']);
        $presenter->redirect('Product:', $parameters);
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



    /**
     * @return ProductParameterGroupEntity[]|array
     */
    protected function getGroupsBySetParameters() : array
    {
        return $this->parameters ? $this->groupParameterRepo->getByMoreId(array_keys($this->parameters)) : [];
    }
}