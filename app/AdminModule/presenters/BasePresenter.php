<?php

namespace App\AdminModule\Presenters;

use App\Components\BreadcrumbNavigation\NameResolverFactory;
use App\Helpers\Presenters;
use App\User\UserFacadeFactory;
use Kdyby\Monolog\Logger;
use Kdyby\Translation\Translator;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class BasePresenter extends Presenter
{


    /** @var UserFacadeFactory @inject */
    public $userFacadeFactory;

    /** @var Context @inject */
    public $database;

    /** @var Logger @inject */
    public $logger;

    /** @var Translator @inject */
    public $translator;

    /** @var NameResolverFactory @inject */
    public $nameResolverFactory;

    /** @var array */
    protected $parameters;



    public function startup()
    {
        parent::startup();
        $this->parameters = $this->context->getParameters();
        $this->translator->setLocale('cs');
    }



    public function beforeRender()
    {
        parent::beforeRender();

        //Default vars
        $this->template->parameters = $this->parameters;

        //Styles
        $this->template->redheadStyleCss = filemtime(__DIR__ . "/../../../www/vendor/redhead-1.0.0/css/style.css");
        $this->template->adminStyleCss = filemtime(sprintf('%s/admin/css/style.css', $this->parameters['assets']['dir']));
        $this->template->selectStyleCss = filemtime(sprintf('%s/admin/css/select2.css', $this->parameters['assets']['dir']));

        //Title for pages
        $this->template->title = $this->nameResolverFactory->create()
            ->getName(Presenters::getRouteFromPresenter($this));
    }



    /**
     * @param $param mixed
     * @param $repositoryFactory string
     * @return IEntity
     * @throws \Exception
     * @throws BadRequestException
     */
    public function checkRequest($param, $repositoryFactory)
    {
        if ($param) {
            $repository = $this->context->getByType($repositoryFactory);
            $repository = $repository instanceof BaseRepository ? $repository : $repository->create();
            if (is_int($param)) {
                $result = $repository->findOneBy(["where" => [["id", "=", $param]]]);
            } else {
                throw new \Exception("Unknow type.");
            }
            if ($result) {
                return $result;
            }
        }
        throw new BadRequestException(null, 404);
    }
}