<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use Nette\Application\AbortException;


/**
 * This presenter checks if user is logged.
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class AbstractLoggedPresenter extends AbstractPresenter
{


    /**
     * @inheritdoc
     * @throws AbortException
     */
    public function startup()
    {
        parent::startup();

        //if user is not logged, redirect him to login page
        if ($this->loggedUser === NULL) {
            $this->flashMessage($this->translator->translate('general.message.signRequire'), 'info');
            $this->redirect(':Front:Sign:in', [self::BACKLINK => $this->storeRequest('+1 hours')]);
        }
    }
}