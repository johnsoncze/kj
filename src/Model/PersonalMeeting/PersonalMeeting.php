<?php

declare(strict_types = 1);

namespace App\PersonalMeeting;

use App\AddDateTrait;
use App\AdminModule\Components\StateChangeForm\IStateObject;
use App\BaseEntity;
use App\StateTrait;
use Kdyby\Translation\ITranslator;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Ricaefeliz\Mappero\Entities\IEntity;


/**

 * @Table(name="personal_meeting")
 *
 * @method getFirstName()
 * @method setFirstName()
 * @method getLastName()
 * @method setLastName()
 * @method getEmail()
 * @method setEmail()
 * @method getTelephone()
 * @method setTelephone()
 * @method getNote()
 * @method setNote()
 * @method getNlConsent()
 * @method setNlConsent()
 * @method getRequestDate()
 * @method setRequestDate()
 */

class PersonalMeeting extends BaseEntity implements IEntity, IStateObject
{

    use AddDateTrait;
    use StateTrait;

    /**
     * @var int|null
     * @Column(name="pm_id", key="Primary")
     */
    protected $id;

    /**
     * @var string
     * @Column(name="pm_name")
     */
    protected $firstName;

    /**
     * @var string
     * @Column(name="pm_surname")
     */
    protected $lastName;

    /**
     * @var string|null
     * @Column(name="pm_email")
     */
    protected $email;

    /**
     * @var string|null
     * @Column(name="pm_phone")
     */
    protected $telephone;

    /**
     * @var string|null
     * @Column(name="pm_preffered_date")
     */
    protected $requestDate;

    /**
     * @var string|null
     * @Column(name="pm_note")
     */
    protected $note;

    /**
     * @var int
     * @Column(name="pm_nl_consent")
     */
    protected $nlConsent;

    /**
     * @var string|null
     * @Column(name="pm_created_at")
     */
    protected $addDate;
		
		
    /**
     * Get absolute page url.
     * @param $linkGenerator LinkGenerator
     * @return string
     */
    public function getAbsolutePageUrl(LinkGenerator $linkGenerator) : string
    {
        $link = '-';
        $page = $this->getPage();

        try {
            if ($page) {
                $page = ltrim($page, ':');
                $args = $this->getPageArguments(TRUE);
                $link = $linkGenerator->link($page, $args);
            }
        } catch (InvalidLinkException $exception) {
            //nothing..
        }

        return $link;
    }



    /**
     * @param $args array|string
     * @return self
     */
    public function setPageArguments($args)
    {
        $this->pageArguments = is_array($args) ? json_encode($args) : $args;
        return $this;
    }



    /**
     * @param $asArray bool
     * @return array|string|null
     */
    public function getPageArguments(bool $asArray = FALSE)
    {
        if ($asArray === TRUE) {
            if ($this->pageArguments) {
                return is_array($this->pageArguments) ? $this->pageArguments : json_decode($this->pageArguments, TRUE);
            }
            return [];
        }
        return $this->pageArguments;
    }

		
    /**
     * @return string
     */
    public function getFullName() : string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

	    
    /**
     * @return string|null
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }
}