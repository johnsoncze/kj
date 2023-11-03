<?php

declare(strict_types = 1);

namespace App\Newsletter\Subscriber;

use App\AddDateTrait;
use App\BaseEntity;
use App\Helpers\Validators;
use Kdyby\Translation\ITranslator;
use Nette\Utils\Random;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="newsletter_subscriber")
 *
 * @method getEmail()
 * @method setConfirmToken($token)
 * @method getConfirmToken()
 * @method setConfirmed($confirmed)
 * @method getConfirmed()
 */
class Subscriber extends BaseEntity implements IEntity
{


    use AddDateTrait;

    /**
     * @var int|null
     * @Column(name="ns_id", key="Primary")
     */
    protected $id;

    /**
     * @var string
     * @Column(name="ns_email")
     */
    protected $email;

    /**
     * @var string|null
     * @Column(name="ns_confirm_token")
     */
    protected $confirmToken;

    /**
     * @var int
     * @Column(name="ns_confirmed")
     */
    protected $confirmed;

    /**
     * @var string
     * @Column(name="ns_add_date")
     */
    protected $addDate;



    /**
     * Is e-mail confirmed?
     * @return bool
     */
    public function isConfirmed() : bool
    {
        return (bool)$this->getConfirmed();
    }



    /**
     * Setter for 'email' property.
     * @param $email string
     * @param $translator ITranslator|null
     * @return self
     * @throws \EntityInvalidArgumentException
     */
    public function setEmail(string $email, ITranslator $translator = NULL) : self
    {
        try{
            \App\Helpers\Validators::checkEmail($email);
        } catch (\InvalidArgumentException $exception){
            $message = $translator !== NULL ? $translator->translate('general.error.invalidEmailFormat', ['email' => $email]) : $exception->getMessage();
            throw new \EntityInvalidArgumentException($message);
        }
        $this->email = $email;
        return $this;
    }



    /**
     * Generate confirm token.
     * @return string
     */
    public static function generateConfirmToken() : string
    {
        return Random::generate(32);
    }
}