<?php

declare(strict_types = 1);

namespace App\PeriskopModule\Presenters;

use Kdyby\Monolog\Logger;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class AbstractPresenter extends Presenter
{


    /** @var Logger */
    public $logger;

    /** @var string @persistent */
    public $token;



    /**
     * @inheritdoc
     * @throws BadRequestException
     */
    public function startup()
    {
        parent::startup();
        if (strtolower($this->token) !== strtolower('KSxxoa9876rsL')) {
            throw new BadRequestException('Invalid access token.', 401);
        }
    }
}