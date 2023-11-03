<?php


namespace App\User;

use App\BaseEntity;
use App\Helpers\Arrays;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @Table(name="user")
 *
 * @method setName($name)
 * @method getName()
 * @method setEmail($email)
 * @method getEmail()
 * @method setPassword($password)
 * @method getPassword()
 * @method getRole()
 * @method setAddDate($addDate)
 * @method getAddDate()
 */
class UserEntity extends BaseEntity implements IEntity
{


    /** @var int */
    const PASSWORD_MIN_LENGTH = 6;

    /** @var string */
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPPLIER = 'supplier';

    /**
     * @Column(name="u_id", key="Primary")
     * @var int
     */
    protected $id;

    /**
     * @Column(name="u_name")
     * @var string
     */
    protected $name;

    /**
     * @Column(name="u_email")
     * @var string
     */
    protected $email;

    /**
     * @Column(name="u_password")
     * @var string
     */
    protected $password;

    /**
     * @Column(name="u_role")
     * @var string
     */
    protected $role;

    /**
     * @Column(name="u_add_date")
     * @var string
     */
    protected $addDate;

    /** @var array */
    protected static $roles = [
        self::ROLE_ADMIN => [
        	'key' => self::ROLE_ADMIN,
			'translation' => 'Admin',
		],
        self::ROLE_SUPPLIER => [
        	'key' => self::ROLE_SUPPLIER,
			'translation' => 'Supplier',
		],
    ];



    /**
     * @return bool
     */
    public function isSupplier() : bool
    {
        return $this->getRole() === self::ROLE_SUPPLIER;
    }



    /**
     * @param $role string
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setRole(string $role)
    {
        if (array_key_exists($role, self::$roles) === FALSE) {
            throw new \InvalidArgumentException(sprintf('Unknown role \'%s\'.', $role));
        }
        $this->role = $role;
        return $this;
    }



    /**
	 * @return array
    */
    public static function getRoleList() : array
	{
		return Arrays::toPair(self::$roles, 'key', 'translation');
	}



	/**
	 * @return string
	*/
	public function getTranslatedRole() : string
	{
		return self::getRoleList()[$this->role];
	}
}