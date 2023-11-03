<?php

namespace App\User;

use App\Extensions\Grido\IRepositorySource;
use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class UserRepository extends BaseRepository implements IRepositorySource
{


    /** @var string entity name */
    protected $entityName = UserEntity::class;



    /**
     * Get by id
     * @param $id int
     * @return UserEntity
     * @throws NotFoundException
     */
    public function getOneById($id)
    {
        if ($id) {
            $result = $this->findOneBy([
                "where" => [
                    ["id", "=", $id]
                ]
            ]);
            if ($result) {
                return $result;
            }
        }
        throw new NotFoundException("User not found.");
    }



    /**
     * Get by email
     * @param $email string
     * @return UserEntity
     * @throws NotFoundException
     */
    public function getOneByEmail($email)
    {
        if ($email) {
            $result = $this->findOneBy([
                "where" => [
                    ["email", "=", $email]
                ]
            ]);
            if ($result) {
                return $result;
            }
        }
        throw new NotFoundException("User not found.");
    }



    /**
     * @param $email string
     * @return UserEntity|null
     */
    public function findOneByEmail($email)
    {
        if ($email) {
            return $this->findOneBy([
                "where" => [
                    ["email", "=", $email]
                ]
            ]);
        }
        return null;
    }


}