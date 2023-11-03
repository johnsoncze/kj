<?php

declare(strict_types = 1);

namespace App\Opportunity;

use App\AddDateTrait;
use App\AdminModule\Components\StateChangeForm\IStateObject;
use App\BaseEntity;
use App\StateTrait;
use Kdyby\Translation\ITranslator;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * todo doplnit lang pro generování odkazu
 *
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="opportunity")
 *
 * @method setCode($code)
 * @method getCode()
 * @method setCustomerId($id)
 * @method getCustomerId()
 * @method setFirstName($name)
 * @method getFirstName()
 * @method setLastName($name)
 * @method getLastName()
 * @method getPreferredContact()
 * @method getEmail()
 * @method setTelephone($telephone)
 * @method setRequestDate($requestDate)
 * @method getTelephone()
 * @method getComment()
 * @method setPage($page)
 * @method getPage()
 * @method setPageId($id)
 * @method setPageBasePathUrl($url)
 * @method getPageBasePathUrl()
 * @method getPageId()
 * @method getState()
 * @method getType()
 */
class Opportunity extends BaseEntity implements IEntity, IStateObject
{


    /** @var int */
    const MAX_LENGTH_COMMENT = 500;

    /** @var string */
    const PREFERRED_CONTACT_EMAIL = 'email';
    const PREFERRED_CONTACT_TELEPHONE = 'telephone';

    /** @var string */
    const STATE_NEW = 'new';
    const STATE_FINISHED = 'finished';

    /** @var string */
    const TYPE_CONTACT_FORM = 'contact_form';
    const TYPE_ORDER_FINISH_ON_STORE = 'order_finish_on_store';
    const TYPE_PRODUCT_DEMAND = 'demand';
    const TYPE_PRODUCT_STORE_MEETING = 'product_store_meeting';
    const TYPE_STORE_MEETING = 'store_meeting';
    const TYPE_WEEDING_RING_DEMAND = 'weeding_ring_demand';

    use AddDateTrait;
    use StateTrait;

    /**
     * @var int|null
     * @Column(name="opp_id", key="Primary")
     */
    protected $id;

    /**
     * @var string
     * @Column(name="opp_code")
     */
    protected $code;

    /**
     * @var int|null
     * @Column(name="opp_customer_id")
     */
    protected $customerId;

    /**
     * @var string
     * @Column(name="opp_first_name")
     */
    protected $firstName;

    /**
     * @var string
     * @Column(name="opp_last_name")
     */
    protected $lastName;

    /**
     * @var string
     * @Column(name="opp_preferred_contact")
     */
    protected $preferredContact;

    /**
     * @var string|null
     * @Column(name="opp_email")
     */
    protected $email;

    /**
     * @var string|null
     * @Column(name="opp_telephone")
     */
    protected $telephone;

    /**
     * @var string|null
     * @Column(name="opp_request_date")
     */
    protected $requestDate;

    /**
     * @var string|null
     * @Column(name="opp_comment")
     */
    protected $comment;

    /**
     * @var string
     * @Column(name="opp_page")
     */
    protected $page;

    /**
     * @var int|null
     * @Column(name="opp_page_id")
     */
    protected $pageId;

    /**
     * @var string|null
     * @Column(name="opp_page_base_path_url")
     */
    protected $pageBasePathUrl;

    /**
     * @var null|string
     * @Column(name="opp_page_arguments")
     */
    protected $pageArguments;

    /**
     * @var string
     * @Column(name="opp_state")
     */
    protected $state;

    /**
     * @var string
     * @Column(name="opp_type")
     */
    protected $type;

    /**
     * @var string|null
     * @Column(name="opp_add_date")
     */
    protected $addDate;


    /** @var array */
    protected static $preferredContactList = [
        self::PREFERRED_CONTACT_EMAIL => [
            'key' => self::PREFERRED_CONTACT_EMAIL,
            'translationKey' => 'opportunity.preferredcontact.email',
        ], self::PREFERRED_CONTACT_TELEPHONE => [
            'key' => self::PREFERRED_CONTACT_TELEPHONE,
            'translationKey' => 'opportunity.preferredcontact.telephone',
        ],
    ];

    /** @var array */
    protected static $states = [
        self::STATE_NEW => [
            'key' => self::STATE_NEW,
            'translationKey' => 'opportunity.state.new',
        ], self::STATE_FINISHED => [
            'key' => self::STATE_FINISHED,
            'translationKey' => 'opportunity.state.finished',
        ],
    ];

    /** @var array */
    protected static $typeList = [
        self::TYPE_CONTACT_FORM => [
            'key' => self::TYPE_CONTACT_FORM,
            'translationKey' => 'opportunity.type.contactform',
            'email' => [
                'subject' => [
                    'translationKey' => 'opportunity.email.contactForm.subject',
                ],
                'template' => 'contact_form.latte',
            ],
        ],

        self::TYPE_ORDER_FINISH_ON_STORE => [
            'key' => self::TYPE_ORDER_FINISH_ON_STORE,
            'translationKey' => 'opportunity.type.storemeeting',
            'email' => [
                'subject' => [
                    'translationKey' => 'opportunity.email.product.subject',
                ],
                'template' => 'store_meeting_with_product.latte',
            ],
        ],

        self::TYPE_PRODUCT_DEMAND => [
            'key' => self::TYPE_PRODUCT_DEMAND,
            'translationKey' => 'opportunity.type.demand',
            'email' => [
                'subject' => [
                    'translationKey' => 'opportunity.email.product.demand.subject',
                ],
                'template' => 'demand.latte',
            ],
        ],

        self::TYPE_PRODUCT_STORE_MEETING => [
            'key' => self::TYPE_PRODUCT_STORE_MEETING,
            'translationKey' => 'opportunity.type.storemeeting',
            'email' => [
                'subject' => [
                    'translationKey' => 'opportunity.email.product.subject',
                ],
                'template' => 'store_meeting_with_product.latte',
            ],
        ],

        self::TYPE_STORE_MEETING => [
            'key' => self::TYPE_STORE_MEETING,
            'translationKey' => 'opportunity.type.storemeeting',
            'email' => [
                'subject' => [
                    'translationKey' => 'opportunity.email.subject',
                ],
                'template' => 'store_meeting.latte',
            ],
        ],

        self::TYPE_WEEDING_RING_DEMAND => [
            'key' => self::TYPE_WEEDING_RING_DEMAND,
            'translationKey' => 'opportunity.type.demand',
            'email' => [
                'subject' => [
                    'translationKey' => 'opportunity.email.product.demand.subject',
                ],
                'template' => 'demand.latte',
            ],
        ],
    ];



    /**
     * Setter for 'preferredContact' property.
     * @param $contact string
     * @return self
     * @throws \InvalidArgumentException unknown type
     */
    public function setPreferredContact(string $contact) : self
    {
        if (!array_key_exists($contact, self::getPreferredContactList())) {
            throw new \InvalidArgumentException(sprintf('Unknown type \'%s\' of preferred contact.', $contact));
        }
        $this->preferredContact = $contact;
        return $this;
    }



    /**
     * Setter for 'type' property.
     * @param $type string
     * @return self
     * @throws \InvalidArgumentException unknown type
     */
    public function setType(string $type) : self
    {
        if (!array_key_exists($type, self::getTypeList())) {
            throw new \InvalidArgumentException(sprintf('Unknown type \'%s\'.', $type));
        }
        $this->type = $type;
        return $this;
    }



    /**
     * @param $email string|null
     * @param $translator ITranslator
     * @return self
     * @throws \EntityInvalidArgumentException
     */
    public function setEmail(string $email = NULL, ITranslator $translator = NULL) : self
    {
        try {
            $email && \App\Helpers\Validators::checkEmail($email);
        } catch (\InvalidArgumentException $exception) {
            $message = $translator !== NULL ? $translator->translate('general.error.invalidEmailFormat', ['email' => $email]) : $exception->getMessage();
            throw new \EntityInvalidArgumentException($message);
        }
        $this->email = $email;
        return $this;
    }



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
     * @param $requestDate string|null
     * @return self
     */
    public function setRequestDate(string $requestDate = NULL) : self
    {
        $this->requestDate = $requestDate;
        return $this;
    }

    /**
     * @param $comment string|null
     * @return self
     */
    public function setComment(string $comment = NULL) : self
    {
        $this->comment = $comment !== NULL ? mb_substr($comment, 0, self::MAX_LENGTH_COMMENT, 'UTF-8') : $comment;
        return $this;
    }



    /**
     * @return string
     */
    public function getFullName() : string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }



    /**
     * Get google analytics event type.
     * @return string
     */
    public function getGtmType() : string
    {
        $search = [
            Opportunity::TYPE_CONTACT_FORM,
            Opportunity::TYPE_ORDER_FINISH_ON_STORE,
            Opportunity::TYPE_PRODUCT_DEMAND,
            Opportunity::TYPE_PRODUCT_STORE_MEETING,
            Opportunity::TYPE_STORE_MEETING,
            Opportunity::TYPE_WEEDING_RING_DEMAND,
        ];
        $replace = [
            'Kontaktní formulář',
            'Košík dokončit na prodejně',
            'Poptávka produktu',
            'Připravit produkt k prohlédnutí',
            'Schůzka na prodejně',
            'Poptávka snubních prstenů',
        ];

        return str_replace($search, $replace, $this->getType());
    }



    /**
     * @throws \EntityInvalidArgumentException missing email subject translation key
     * @return string
     */
    public function getEmailSubjectTranslationKey() : string
    {
        $values = self::getTypeList()[$this->getType()] ?? [];
        $key = $values['email']['subject']['translationKey'] ?? NULL;

        if ($key === NULL) {
            throw new \EntityInvalidArgumentException('Missing subject translation key.');
        }

        return $key;
    }



    /**
     * @throws \EntityInvalidArgumentException missing email template name
     * @return string
     */
    public function getEmailTemplateName() : string
    {
        $values = self::getTypeList()[$this->getType()] ?? [];
        $name = $values['email']['template'] ?? NULL;

        if ($name === NULL) {
            throw new \EntityInvalidArgumentException('Missing template name.');
        }

        return $name;
    }



    /**
     * @return array
     */
    public static function getPreferredContactList() : array
    {
        return self::$preferredContactList;
    }



    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getPreferredContactTranslationKey() : string
    {
        $values = self::getPreferredContactValues($this->getPreferredContact());
        return $values['translationKey'];
    }



    /**
     * @param $type string
     * @return bool
     */
    public function isType(string $type) : bool
    {
        return $this->getType() === $type;
    }



    /**
     * @param $translator ITranslator
     * @return array
     */
    public static function getTranslatedPreferredContactList(ITranslator $translator) : array
    {
        $list = [];
        foreach (self::getPreferredContactList() as $value) {
            $list[$value['key']] = $translator->translate($value['translationKey']);
        }
        return $list;
    }



    /**
     * @param $contact string
     * @return array
     * @throws \InvalidArgumentException unknown contact
     */
    public static function getPreferredContactValues(string $contact) : array
    {
        $list = self::getPreferredContactList();
        if (!isset($list[$contact])) {
            throw new \InvalidArgumentException(sprintf('Unknown contact \'%s\'.', $contact));
        }
        return $list[$contact];
    }



    /**
     * @return array
     */
    public static function getTypeList() : array
    {
        return self::$typeList;
    }

    
    /**
     * @return string|null
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }
}