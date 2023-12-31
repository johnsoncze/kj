<?php

namespace App\Extensions\Latte;

use App\Libs\FileManager\FileManager;
use Nette;
use Nette\Application\UI;
use Latte;



/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class MyTemplateFactory implements UI\ITemplateFactory
{


    use Nette\SmartObject;

    /** @var FileManager */
    private $fileManager;

    /** @var Nette\Bridges\ApplicationLatte\ILatteFactory */
    private $latteFactory;

    /** @var Nette\Application\LinkGenerator */
    private $linkGenerator;

    /** @var Nette\Http\IRequest */
    private $httpRequest;

    /** @var Nette\Security\User */
    private $user;

    /** @var Nette\Caching\IStorage */
    private $cacheStorage;

    /** @var string */
    private $templateClass;



    public function __construct(FileManager $fileManager, Nette\Bridges\ApplicationLatte\ILatteFactory $latteFactory, Nette\Http\IRequest $httpRequest = NULL,
                                Nette\Security\User $user = NULL, Nette\Caching\IStorage $cacheStorage = NULL, Nette\Application\LinkGenerator $linkGenerator,
                                $templateClass = NULL)
    {
        $this->fileManager = $fileManager;
        $this->latteFactory = $latteFactory;
        $this->linkGenerator = $linkGenerator;
        $this->httpRequest = $httpRequest;
        $this->user = $user;
        $this->cacheStorage = $cacheStorage;
        if ($templateClass && (!class_exists($templateClass) || !is_a($templateClass, Nette\Bridges\ApplicationLatte\Template::class, TRUE))) {
            throw new Nette\InvalidArgumentException("Class $templateClass does not extend " . Nette\Bridges\ApplicationLatte\Template::class . ' or it does not exist.');
        }
        $this->templateClass = $templateClass ?: Nette\Bridges\ApplicationLatte\Template::class;
    }



    /**
     * @return Nette\Bridges\ApplicationLatte\Template
     */
    public function createTemplate(UI\Control $control = NULL)
    {
        $latte = $this->latteFactory->create();
        $template = new $this->templateClass($latte);
        $presenter = $control ? $control->getPresenter(FALSE) : NULL;

        if ($control instanceof UI\Presenter) {
            $latte->setLoader(new \Nette\Bridges\ApplicationLatte\Loader($control));
        }

        if ($latte->onCompile instanceof \Traversable) {
            $latte->onCompile = iterator_to_array($latte->onCompile);
        }

        array_unshift($latte->onCompile, function ($latte) use ($control, $template) {
            $latte->getCompiler()->addMacro('cache', new Nette\Bridges\CacheLatte\CacheMacro($latte->getCompiler()));
            Nette\Bridges\ApplicationLatte\UIMacros::install($latte->getCompiler());
            \App\Extensions\Latte\MacroSet::install($latte->getCompiler());
            if (class_exists(Nette\Bridges\FormsLatte\FormMacros::class)) {
                Nette\Bridges\FormsLatte\FormMacros::install($latte->getCompiler());
            }
            if ($control) {
                $control->templatePrepareFilters($template);
            }
        });

        $latte->addFilter('url', 'rawurlencode'); // back compatiblity
        foreach (['normalize', 'toAscii', 'webalize', 'padLeft', 'padRight', 'reverse'] as $name) {
            $latte->addFilter($name, 'Nette\Utils\Strings::' . $name);
        }
        $latte->addFilter('null', function () {
        });
        $latte->addFilter('modifyDate', function ($time, $delta, $unit = NULL) {
            return $time == NULL ? NULL : Nette\Utils\DateTime::from($time)->modify($delta . $unit); // intentionally ==
        });

        if (!isset($latte->getFilters()['translate'])) {
            $latte->addFilter('translate', function (Latte\Runtime\FilterInfo $fi) {
                throw new Nette\InvalidStateException('Translator has not been set. Set translator using $template->setTranslator().');
            });
        }

        // default parameters
        $template->deviceDetect = new \Mobile_Detect();
        $template->fileManager = $this->fileManager;
        $template->linkGenerator = $this->linkGenerator;
        $template->user = $this->user;
        $template->baseUri = $template->baseUrl = $this->httpRequest ? rtrim($this->httpRequest->getUrl()->getBaseUrl(), '/') : NULL;
        $template->basePath = preg_replace('#https?://[^/]+#A', '', $template->baseUrl);
        $template->flashes = [];
        if ($control) {
            $template->control = $control;
            $template->presenter = $presenter;
            $latte->addProvider('uiControl', $control);
            $latte->addProvider('uiPresenter', $presenter);
            $latte->addProvider('snippetBridge', new Nette\Bridges\ApplicationLatte\SnippetBridge($control));
        }
        $latte->addProvider('cacheStorage', $this->cacheStorage);

        // back compatibility
        $template->_control = $control;
        $template->_presenter = $presenter;
        $template->netteCacheStorage = $this->cacheStorage;

        if ($presenter instanceof UI\Presenter && $presenter->hasFlashSession()) {
            $id = $control->getParameterId('flash');
            $template->flashes = (array)$presenter->getFlashSession()->$id;
        }

        return $template;
    }

}
