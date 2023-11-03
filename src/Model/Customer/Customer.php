<?php

declare(strict_types = 1);

namespace App\Customer;

use App\AddDateTrait;
use App\BaseEntity;
use App\ExternalSystemIdTrait;
use App\ShoppingCart\ShoppingCartDiscount;
use App\StateTrait;
use App\UpdateDateTrait;
use Kdyby\Translation\ITranslator;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Entities\Interfaces\IAllow;
use Ricaefeliz\Mappero\Entities\Traits\AllowTrait;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="customer")
 *
 * @method getExternalSystemId()
 * @method getFirstName()
 * @method getLastName()
 * @method getSex()
 * @method setAddressing($addressing)
 * @method getAddressing()
 * @method getEmail()
 * @method setTelephone($telephone)
 * @method getTelephone()
 * @method setStreet($street)
 * @method getStreet()
 * @method setCity($city)
 * @method getCity()
 * @method setPostcode($postcode)
 * @method getPostcode()
 * @method setCountryCode($code)
 * @method getCountryCode()
 * @method getBirthdayYear()
 * @method getBirthdayMonth()
 * @method getBirthdayDay()
 * @method setBirthdayCoupon($coupon)
 * @method getBirthdayCoupon()
 * @method setBirthdayCouponLastUse($couponLastUse)
 * @method getBirthdayCouponLastUse()
 * @method setNewsletter($newsletter)
 * @method getNewsletter()
 * @method setPassword($password)
 * @method getPassword()
 * @method getActivationToken()
 * @method getActivationDate()
 * @method setExternalSystemLastChangeDate($date)
 * @method getExternalSystemLastChangeDate()
 * @method setHearAboutUs($value)
 * @method getHearAboutUs()
 * @method setHearAboutUsComment($comment)
 * @method getHearAboutUsComment()
 * @method setPasswordSuffixId($id)
 * @method getPasswordSuffixId()
 * @method setCode($code)
 * @method getCode()
 * @method getAddDate()
 * @method getId()
 */
class Customer extends BaseEntity implements IEntity, IAllow
{


    /** @var int */
    const BIRTHDAY_DISCOUNT = ShoppingCartDiscount::BIRTHDAY_COUPON_DISCOUNT;
    const DISCOUNT = 10;

    use AddDateTrait;
    use AllowTrait;
    use ExternalSystemIdTrait;
    use StateTrait;
    use UpdateDateTrait;

    /**
     * @Column(name="cus_id", key="Primary")
     */
    protected $id;

    /**
     * @var int|null
     * @Column(name="cus_external_system_id")
     */
    protected $externalSystemId;

    /**
     * @var string
     * @Column(name="cus_first_name")
     */
    protected $firstName;

    /**
     * @var string
     * @Column(name="cus_last_name")
     */
    protected $lastName;

    /**
     * @var string|null
     * @Column(name="cus_sex")
     */
    protected $sex;

    /**
     * @var string|null
     * @Column(name="cus_addressing")
     */
    protected $addressing;

    /**
     * @var string
     * @Column(name="cus_email")
     */
    protected $email;

    /**
     * @var string|null
     * @Column(name="cus_telephone")
     */
    protected $telephone;

    /**
     * @var string|null
     * @Column(name="cus_street")
     */
    protected $street;

    /**
     * @var string|null
     * @Column(name="cus_city")
     */
    protected $city;

    /**
     * @var string|null
     * @Column(name="cus_postcode")
     */
    protected $postcode;

    /**
     * @var string|null
     * @Column(name="cus_country_code")
     */
    protected $countryCode;

    /**
     * @var int|null
     * @Column(name="cus_birthday_year")
     */
    protected $birthdayYear;

    /**
     * @var int|null
     * @Column(name="cus_birthday_month")
     */
    protected $birthdayMonth;

    /**
     * @var int|null
     * @Column(name="cus_birthday_day")
     */
    protected $birthdayDay;

    /**
     * @var int
     * @Column(name="cus_birthday_coupon")
     */
    protected $birthdayCoupon;


    /**
     * @var string|null
     * @Column(name="cus_birthday_coupon_last_use")
     */
    protected $birthdayCouponLastUse;
		
		
    /**
     * @var int
     * @Column(name="cus_newsletter")
     */
    protected $newsletter;

    /**
     * @var string|null
     * @Column(name="cus_password")
     */
    protected $password;

    /**
     * @var string|null
     * @Column(name="cus_activation_token")
     */
    protected $activationToken;

    /**
     * @var string|null
     * @Column(name="cus_activation_token_valid_to")
     */
    protected $activationTokenValidTo;

    /**
     * @var string|null
     * @Column(name="cus_activation_date")
     */
    protected $activationDate;

    /**
     * @var string|null
     * @Column(name="cus_external_system_last_change_date")
     */
    protected $externalSystemLastChangeDate;

    /**
     * @var string|null
     * @Column(name="cus_hear_about_us")
     */
    protected $hearAboutUs;

    /**
     * @var string|null
     * @Column(name="cus_hear_about_us_comment")
     */
    protected $hearAboutUsComment;

    /**
     * Created for customer id from previous online shop,
     * because we wanted to keep customer's password
     * and the algorithm works with the id.
     *
     * @var int|null
     * @Column(name="cus_password_suffix_id")
    */
    protected $passwordSuffixId;

    /**
     * @var string
     * @Column(name="cus_state")
     */
    protected $state;

    /**
     * @var string
     * @Column(name="cus_code")
     */
    protected $code;

    /**
     * @var string|null
     * @Column(name="cus_add_date")
    */
    protected $addDate;

    /**
     * @Column(name="cus_updated_date", type="timestamp")
    */
    protected $updateDate;

    /**
     * List of sex values.
     * @var array
     */
    protected static $sexList = [
        'm' => 'Muž',
        'z' => 'Žena',
    ];



    /**
     * Setter for 'firstName' property.
     * @param $name string
     * @return self
     * @throws \EntityInvalidArgumentException bad format
     */
    public function setFirstName(string $name) : self
    {
        if (!$name) {
            throw new \EntityInvalidArgumentException('First name can not be empty.');
        }
        $this->firstName = $name;
        return $this;
    }



    /**
     * Setter for 'lastName' property.
     * @param $name string
     * @return self
     * @throws \EntityInvalidArgumentException bad format
     */
    public function setLastName(string $name) : self
    {
        if (!$name) {
            throw new \EntityInvalidArgumentException('Last name can not be empty.');
        }
        $this->lastName = $name;
        return $this;
    }



    /**
     * Setter for e-mail property.
     * @param $email string
     * @param $translator ITranslator|null
     * @return self
     * @throws \EntityInvalidArgumentException
     */
    public function setEmail(string $email, ITranslator $translator = NULL) : self
    {
        try {
            \App\Helpers\Validators::checkEmail($email);
        } catch (\InvalidArgumentException $exception) {
            $message = $translator !== NULL ? $translator->translate('general.error.invalidEmailFormat', ['email' => $email]) : $exception->getMessage();
            throw new \EntityInvalidArgumentException($message);
        }
        $this->email = $email;
        return $this;
    }



    /**
     * Setter for sex property.
     * @param $sex string
     * @return self
     * @throws \EntityInvalidArgumentException in case of unknown value
     */
    public function setSex(string $sex = NULL) : self
    {
        if ($sex !== NULL && !array_key_exists($sex, self::getSexList())) {
            throw new \EntityInvalidArgumentException('Neznámý typ pohlaví.');
        }
        $this->sex = $sex;
        return $this;
    }



    /**
     * Setter for externalSystemId property.
     * @param $id int|null
     * @return self
     * @throws \EntityInvalidArgumentException
     */
    public function setExternalSystemId(int $id = NULL) : self
    {
        if ($id !== NULL && $this->isValidExternalSystemId($id) !== TRUE) {
            throw new \EntityInvalidArgumentException('Externí id musí být větší než 0.');
        }
        $this->externalSystemId = $id;
        return $this;
    }



    /**
     * Is customer activated?
     * @return bool
     */
    public function isActivated() : bool
    {
        return (bool)$this->getPassword();
    }



    /**
     * Setter for 'birthdayYear' property.
     * @param $year mixed
     * @return self
     * @throws \EntityInvalidArgumentException bad format
     */
    public function setBirthdayYear($year) : self
    {
        if ($year !== NULL) {
            if (strlen((string)$year) < 4) {
                throw new \EntityInvalidArgumentException(sprintf('Invalid birthday year \'%d\'. Year must have four numbers.', $year));
            }
            if ($year > date('Y')) {
                throw new \EntityInvalidArgumentException(sprintf('Birthday year \'%d\' can not be future.', $year));
            }
        }
        $this->birthdayYear = $year;
        return $this;
    }



    /**
     * Setter for 'birthdayMonth' property.
     * @param $month mixed
     * @return self
     * @throws \EntityInvalidArgumentException bad format
     */
    public function setBirthdayMonth($month) : self
    {
        if ($month !== NULL) {
            $month = sprintf('%02s', $month);
            $months = self::getMonthList();
            if (!array_key_exists($month, $months)) {
                throw new \EntityInvalidArgumentException(sprintf('Birthday month \'%s\' is ouf of range %s.', $month, implode(', ', $months)));
            }
        }
        $this->birthdayMonth = $month;
        return $this;
    }



    /**
     * Setter for 'birthdayDay' property.
     * @param $day mixed
     * @return self
     * @throws \EntityInvalidArgumentException bad format
     */
    public function setBirthdayDay($day)
    {
        if ($day !== NULL) {
            $day = sprintf('%02s', $day);
            $days = self::getMonthDayList();
            if (!array_key_exists($day, $days)) {
                throw new \EntityInvalidArgumentException(sprintf('Birthday day \'%s\' is ouf of range %s.', $day, implode(', ', $days)));
            }
        }
        $this->birthdayDay = $day;
        return $this;
    }



    /**
     * Does have customer birthday coupon?
     * @return bool
     */
    public function hasBirthdayCoupon() : bool
    {
        return (bool)$this->getBirthdayCoupon();
    }



    /**
     * Does customer want newsletter?
     * @return bool
     */
    public function wantNewsletter() : bool
    {
        return (bool)$this->getNewsletter();
    }



    /**
     * Setter for activationToken property.
     * @param $token string|null
     * @return self
     * @throws \EntityInvalidArgumentException in case of customer is activated
     */
    public function setActivationToken(string $token = NULL) : self
    {
        if ($token !== NULL && $this->isActivated() === TRUE) {
            $this->customerActivatedException();
        }
        $this->activationToken = $token;
        return $this;
    }



    /**
     * Setter for activationTokenValidTo property.
     * @param $date string|null
     * @return self
     * @throws \EntityInvalidArgumentException in case of customer is activated
     */
    public function setActivationTokenValidTo(string $date = NULL) : self
    {
        if ($date !== NULL && $this->isActivated() === TRUE) {
            $this->customerActivatedException();
        }
        $this->activationTokenValidTo = $date;
        return $this;
    }



    /**
     * Setter for activationDate property.
     * @param $date string
     * @return self
     * @throws \InvalidArgumentException date is set already
     */
    public function setActivationDate(string $date) : self
    {
        if ($this->getActivationDate() !== NULL) {
            throw new \InvalidArgumentException('Date can not be changed.');
        }
        $this->activationDate = $date;
        return $this;
    }



    /**
     * Get full name.
     * @return string|null
     */
    public function getFullName()
    {
        return $this->getLastName() . ' ' . $this->getFirstName();
    }



    /**
     * Get customer's age.
     * @return int|null
     */
    public function getAge()
    {
        $actualDay = (int)date('d');
        $actualMonth = (int)date('m');
        $actualYear = (int)date('Y');
        $year = (int)$this->getBirthdayYear();
        $month = (int)$this->getBirthdayMonth();
        $day = (int)$this->getBirthdayDay();

        $age = NULL;
        if ($year) {
            $age = $actualYear - $year;
            $age -= $month !== NULL && $actualMonth < $month ? 1 : 0;
            $age -= $day !== NULL && $month !== NULL && $actualMonth === $month && $actualDay < $day ? 1 : 0;
        }
        return $age;
    }



    /**
     * Get sex list.
     * @return array
     */
    public static function getSexList() : array
    {
        return self::$sexList;
    }



    /**
     * Getter for activationTokenValidTo property.
     * @param $required bool
     * @return string|null
     * @throws \InvalidArgumentException token is required but is null
     */
    public function getActivationTokenValidTo(bool $required = FALSE)
    {
        if ($required === TRUE && $this->activationTokenValidTo === NULL) {
            throw new \InvalidArgumentException('Missing activation token.');
        }
        return $this->activationTokenValidTo;
    }



    /**
     * Does customer have valid activation request?
     * @return bool
    */
    public function hasValidActivationRequest() : bool
    {
        $activationTokenValidTo = $this->getActivationTokenValidTo();
        return $this->isActivated() !== TRUE && $activationTokenValidTo !== NULL && $activationTokenValidTo > (new \DateTime())->format('Y-m-d H:i:s');
    }



    /**
     * @return string|null
    */
    public function getFormattedPostCode()
    {
        $postCode = $this->getPostcode();
        return $postCode ? substr_replace($postCode, ' ', 3, 0) : NULL;
    }



    /**
     * Get month day list.
     * @return array
     */
    public static function getMonthDayList() : array
    {
        $days = [];
        for ($i = 1; $i <= 31; $i++) {
            $key = sprintf('%02s', $i);
            $days[$key] = $key;
        }
        return $days;
    }



    /**
     * Get month list.
     * @param $translator ITranslator|null
     * @return array
     */
    public static function getMonthList(ITranslator $translator = NULL) : array
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $key = sprintf('%02s', $i);
            $translationKey = 'calendar.month.' . $i;
            $months[$key] = $translator ? $translator->translate($translationKey) : $i;
        }
        return $months;
    }



    /**
     * @param $translator ITranslator
     * @return array
     */
    public static function getHearAboutUsList(ITranslator $translator) : array
    {
        return [
            1 => $translator->translate('form.hearAboutUs.personal'),
            2 => $translator->translate('form.hearAboutUs.printedMedia'),
            3 => $translator->translate('form.hearAboutUs.event'),
            4 => $translator->translate('form.hearAboutUs.internet'),
            5 => $translator->translate('form.hearAboutUs.other'),
        ];
    }


    /**
     * Should customer have a birthday discount?
     * @return bool
    */
    public function shouldHaveBirthDayCoupon() : bool
    {
				if (!$this->getBirthdayMonth() || !$this->getBirthdayYear()) {
						return false;
				}
				
				//pokud mam registraci dele nez rok 
				$lastYearToday = date("Y-m-d", strtotime("-1 year", time()));
				if ($this->getAddDate() <= $lastYearToday) {
						//nikdy jsem slevu nepouzil
						if (!$this->getBirthdayCouponLastUse()) {
								return true;
						}
					
						//od minulych narozenin, jsem neudelal nakup (s aplikovanym kuponem)
						if ($this->getBirthdayMonth() > date("m")) {
								$lastYear = date("Y") - 1;
								$lastBirthday = $lastYear."-".$this->getBirthdayMonth();		
						}
						else {
								$lastBirthday = date("Y")."-".$this->getBirthdayMonth();											
						}
						$BirthdayCouponLastUseMonth = date("Y-m-d", strtotime($this->getBirthdayCouponLastUse()));

						if ($BirthdayCouponLastUseMonth > $lastBirthday) {
								return false;
						}
						
						return true;
				}
				//registrace kratsi dobu nez rok -> slevu dostanu v az mesici svych narozenin
				else {
						$currentMonth = date("m");
						if ($currentMonth == $this->getBirthdayMonth()) {
								return true;
						}
				}
								
				return false;
		}

		
		
    /**
     * @throws \EntityInvalidArgumentException
     */
    protected function customerActivatedException()
    {
        throw new \EntityInvalidArgumentException('The customer is activated already.');
    }

}
