<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Search\Form;

use App\FrontModule\Components\FormSpamProtection;
use App\Google\TagManager\DataLayer;
use Kdyby\Translation\ITranslator;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SearchForm extends Control
{


    use FormSpamProtection;

    /** @var DataLayer */
    private $dataLayer;

    /** @var ITranslator */
    private $translator;

    /** @var string|null */
    private $query;

    /** @var int */
    private $resultCount = 0;



    public function __construct(DataLayer $dataLayer,
                                ITranslator $translator)
    {
        parent::__construct();
        $this->dataLayer = $dataLayer;
        $this->translator = $translator;
    }



    /**
     * @param $query string
     * @return self
     */
    public function setQuery(string $query) : self
    {
        $this->query = $query;
        return $this;
    }



    /**
     * @param $count int
     * @return self
     */
    public function setResultCount(int $count) : self
    {
        $this->resultCount = $count;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $form = new Form();
        $this->addSpamProtection($form);
        $form->addText('query', $this->translator->translate('search.form.input.query.label'))
            ->setRequired($this->translator->translate('search.form.input.query.message.require'))
            ->setAttribute('placeholder', $this->translator->translate('search.form.input.query.message.placeholder'))
            ->setMaxLength(255)
            ->setDefaultValue($this->query);
        $form->addSubmit('submit', $this->translator->translate('search.form.input.submit.label'));
        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }



    /**
     * @param $form Form
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        //if is set default query, user is probably on search page
        $isSearchPage = (bool)$this->query;
        $values = $form->getValues();
        $presenter = $this->getPresenter();
        $this->processSpamRequest($values, $presenter, $this->translator);

        //add data for google analytics
        $this->dataLayer->add([
            'hledani-dotaz' => $values->query,
            'hledani-misto' => $isSearchPage ? 'stránka hledání' : 'hlavička',
            'event' => 'Search',
        ]);

        $route = $isSearchPage ? 'this' : 'Search:default';
        $urlParams = ['query' => $values->query];
        $presenter->redirect($route, $urlParams);
    }



    public function render()
    {
        $this->template->query = $this->query;
        $this->template->resultCount = $this->resultCount;

        $this->template->setFile(__DIR__ . '/templates/result.latte');
        $this->template->render();
    }



    public function renderSmall()
    {
        $this->template->setFile(__DIR__ . '/templates/small.latte');
        $this->template->render();
    }
}