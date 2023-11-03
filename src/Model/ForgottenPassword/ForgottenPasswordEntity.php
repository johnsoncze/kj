<?php


namespace App\ForgottenPassword;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Exceptions\EntityException;
use Nette\Utils\DateTime;


/**
 * @Table(name="forgotten_password")
 *
 * @method setUserId($userId)
 * @method getUserId()
 * @method setCustomerId($customerId)
 * @method getCustomerId()
 * @method setHash($hash)
 * @method getHash()
 * @method setAddDate($addDate)
 * @method getAddDate()
 * @method setValidityDate($date)
 */
class ForgottenPasswordEntity extends BaseEntity implements IEntity
{


    /** @var int validity in minutes */
    const VALIDITY = "30";

    /**
     * @Column(name="fp_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="fp_user_id")
     */
    protected $userId;

    /**
     * @Column(name="fp_customer_id")
     */
    protected $customerId;

    /**
     * @Column(name="fp_hash")
     */
    protected $hash;

    /**
     * @Column(name="fp_add_date")
     */
    protected $addDate;

    /**
     * @Column(name="fp_validity_date")
     */
    protected $validityDate;



    /**
     * Get validity date
     * @return string
     * @throws EntityException
     */
    public function getValidityDate()
    {
        if (!$this->validityDate) {
            $date = new DateTime();
            $interval = new \DateInterval("PT" . self::VALIDITY . "M");
            $date->add($interval);
            $this->validityDate = $date->format("Y-m-d H:i:s");
        }
        return $this->validityDate;
    }
}