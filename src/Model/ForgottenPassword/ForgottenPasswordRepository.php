<?php

namespace App\ForgottenPassword;

use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


class ForgottenPasswordRepository extends BaseRepository
{


    /** @var string */
    protected $entityName = ForgottenPasswordEntity::class;



    /**
     * @param $userId int
     * @param $hash string
     * @return ForgottenPasswordEntity
     * @throws NotFoundException
     */
    public function getOneByUserIdAndHash($userId, $hash)
    {
        if ($userId && $hash) {
            $result = $this->findOneBy([
                "where" => [
                    ["userId", "=", $userId],
                    ["hash", "=", $hash]
                ]
            ]);
            if ($result) {
                return $result;
            }
        }
        throw new NotFoundException("Result not found.");
    }



    /**
     * @param $customerId int
     * @param $hash string
     * @return ForgottenPasswordEntity
     * @throws NotFoundException
     */
    public function getOneByCustomerIdAndHash(int $customerId, string $hash) : ForgottenPasswordEntity
    {
        $filter['where'][] = ['customerId', '=', $customerId];
        $filter['where'][] = ['hash', '=', $hash];
        $result = $this->findOneBy($filter);
        if (!$result) {
            throw new NotFoundException('Result not found.');
        }
        return $result;
    }



    /**
     * @param $userId int
     * @return ForgottenPasswordEntity[]|null
     */
    public function findByUserId($userId)
    {
        if ($userId) {
            return $this->findBy([
                "where" => [
                    ["userId", "=", $userId]
                ]
            ]);
        }
        return null;
    }



    /**
     * @param $id int
     * @return ForgottenPasswordEntity[]|array
     */
    public function findByCustomerId(int $id) : array
    {
        $filter['where'][] = ['customerId', '=', $id];
        return $this->findBy($filter) ?: [];
    }
}