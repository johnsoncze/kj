<?php

namespace App\Components;

use Kdyby\Translation\ITranslator;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\ITemplateFactory;
use Nette\Application\UI\Presenter;
use Nette\Forms\Container;
use Nette\UnexpectedValueException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class BaseFormContainer extends Container
{


    /** @var ITemplateFactory */
    protected $templateFactory;

    /** @var ITemplate */
    protected $template;

    /** @var array */
    protected $templateParameters = [];

    /** @var ITranslator */
    protected $translator;



    public function __construct(ITranslator $translator,
                                ITemplateFactory $templateFactory)
    {
        parent::__construct();
        $this->templateFactory = $templateFactory;
        $this->translator = $translator;
        $this->configure();
    }



    /**
     * @param $name string
     * @param $value mixed
     * @return $this
     */
    public function addTemplateParameter(string $name, $value)
    {
        $this->templateParameters[$name] = $value;
        return $this;
    }



    /**
     * @return ITemplate
     */
    public function getTemplate()
    {
        if ($this->template === NULL) {
            $value = $this->createTemplate();
            if (!$value instanceof ITemplate && $value !== NULL) {
                $class2 = get_class($value);
                $class = get_class($this);
                throw new UnexpectedValueException("Object returned by $class::createTemplate() must be instance of Nette\\Application\\UI\\ITemplate, '$class2' given.");
            }
            $this->template = $value;
            $this->addTemplateParameters($this->template);
        }
        return $this->template;
    }



    protected function createTemplate()
    {
        $presenter = $this->lookup(Presenter::class);
        $templateFactory = $this->templateFactory ?: $this->lookup(Presenter::class)->getTemplateFactory();
        return $templateFactory->createTemplate($presenter);
    }



    abstract protected function configure();



    abstract public function render();



    /**
     * @param ITemplate $template
     * @return ITemplate
     */
    protected function addTemplateParameters(ITemplate $template)
    {
        foreach ($this->templateParameters as $name => $parameter) {
            $template->{$name} = $parameter;
        }
        return $template;
    }
}