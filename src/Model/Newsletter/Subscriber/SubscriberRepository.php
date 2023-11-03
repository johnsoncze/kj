<?php

declare(strict_types = 1);

namespace App\Newsletter\Subscriber;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use App\Newsletter\SubscriberNotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SubscriberRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = Subscriber::class;



    /**
     * @param $email string
     * @param $token string
     * @return Subscriber
     * @throws SubscriberNotFoundException
     */
    public function getOneNoConfirmedByEmailAndConfirmToken(string $email, string $token) : Subscriber
    {
        $filters['where'][] = ['email', '=', $email];
        $filters['where'][] = ['confirmToken', '=', $token];
        $filters['where'][] = ['confirmed', '=', FALSE];
        $subscriber = $this->findOneBy($filters);
        if (!$subscriber) {
            throw new SubscriberNotFoundException('Subscriber not found.');
        }
        return $subscriber;
    }



    /**
     * @param $email string
     * @return Subscriber|null
     */
    public function findOneByEmail(string $email)
    {
        $filters['where'][] = ['email', '=', $email];
        return $this->findOneBy($filters) ?: NULL;
    }
}