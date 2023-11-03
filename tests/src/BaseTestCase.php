<?php

namespace App\Tests;

use App\IRepository;
use Kdyby\Translation\Latte\TranslateMacros;
use Kdyby\Translation\TemplateHelpers;
use Kdyby\Translation\Translator;
use Nette\Application\Application;
use Nette\Application\LinkGenerator;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\Database\Context;
use Nette\DI\Container;
use Ricaefeliz\Mappero\Entities\IEntity;
use Tester\TestCase;


abstract class BaseTestCase extends TestCase
{


    /** @var Container */
    protected $container;

    /** @var Context */
    protected $database;

    /** @var array */
    private $entitiesForClean = [];



    protected function createContainer()
    {
        global $container;
        $this->container = $container;
    }



    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->createContainer();
        $this->database = $this->container->getByType(Context::class);
    }



    /**
     * Call callback with database query which can be call without foreign keys check.
     * @param $callback callable
     * @return mixed response from callback
     */
    protected function runDatabaseQueryWithoutForeignKeysCheck(callable $callback)
    {
        $this->database->query('SET foreign_key_checks = 0');
        $response = $callback();
        $this->database->query('SET foreign_key_checks = 1');

        return $response;
    }



    /**
     * Save without foreign keys check.
     * @param $entity IEntity[]|IEntity
     * @param $repository IRepository
     * @return IEntity[]|IEntity
     */
    protected function saveWithoutForeignKeysCheck($entity, IRepository $repository)
    {
        $this->runDatabaseQueryWithoutForeignKeysCheck(function () use ($repository, $entity) {
            $repository->save($entity);
        });
        return $entity;
    }



    /**
     * Add entity for remove from storage.
     * @param $entity IEntity
     * @param $repository IRepository
     * @return self
     */
    protected function addEntityForRemove(IEntity $entity, IRepository $repository) : self
    {
        $this->entitiesForClean[] = [$entity, $repository];
        return $this;
    }



    /**
     * Get entities for remove.
     * @return array
     */
    protected function getEntitiesForRemove() : array
    {
        return $this->entitiesForClean;
    }



    /**
     * Remove entities.
     * @return int count of removed entities
     */
    protected function removeEntities() : int
    {
        /**
         * @var $entity IEntity
         * @var $repository IRepository
         */
        $i = 0;
        foreach ($this->getEntitiesForRemove() as list($entity, $repository)) {
            $repository->remove($entity);
            $i++;
        }
        return $i;
    }



    /**
     * Render latte template to string.
     * @param $path string absolute path of template
     * @param $params array
     * @return string
     */
    protected function templateToString(string $path, array $params = []) : string
    {
        $application = $this->container->getByType(Application::class);
        $linkGenerator = $this->container->getByType(LinkGenerator::class);

        $latte = new \Latte\Engine;
		(new TemplateHelpers($this->container->getByType(Translator::class)))->register($latte);
        \app\extensions\Latte\MacroSet::install($latte->getCompiler());
        UIMacros::install($latte->getCompiler());
		TranslateMacros::install($latte->getCompiler());
        $presenter = $application->getPresenter();
        $latte->addProvider("uiPresenter", $presenter);
        $latte->addProvider('uiControl', $linkGenerator);

        return $latte->renderToString($path, $params);
    }



    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->removeEntities();
    }
}