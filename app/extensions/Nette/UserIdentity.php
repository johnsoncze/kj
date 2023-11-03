<?php

namespace App\Extensions\Nette;

use App\NObject;
use Nette\Security\IIdentity;


/**
 * @method setId($id)
 * @method setRoles($roles)
 * @method setEntity($entity)
 * @method getEntity()
 */
class UserIdentity extends NObject implements IIdentity
{


    /** @var int */
    protected $id;

    /** @var array */
    protected $roles = [];

    /** @var \App\User\UserEntity|\App\Customer\Customer|null */
    protected $entity;



    /**
     * Returns the ID of user.
     * @return mixed
     */
    function getId()
    {
        return $this->id;
    }



    /**
     * Returns a list of roles that the user is a member of.
     * @return array
     */
    function getRoles()
    {
        return $this->roles;
    }
}