<?php

declare(strict_types = 1);

namespace App\Store\OpeningHours\Change;

use App\NotFoundException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ChangeFacade
{


    /** @var ChangeRepository */
    private $changeRepo;



    public function __construct(ChangeRepository $changeRepo)
    {
        $this->changeRepo = $changeRepo;
    }



    /**
     * Add new change.
     * @param $date string
     * @param $openingTime string|null
     * @param $closingTime string|null
     * @return Change
     * @throws ChangeFacadeException
     */
    public function add(string $date, string $openingTime = NULL, string $closingTime = NULL) : Change
    {
        try {
            $dateObject = new \DateTime($date);
        } catch (\Exception $exception) {
            throw new ChangeFacadeException('Datum nemá správný formát.');
        }

        $change = $this->changeRepo->findOneByDate($dateObject);
        if ($change) {
            throw new ChangeFacadeException(sprintf('Vyjímka pro datum \'%s\' již existuje.', $dateObject->format('d.m.Y')));
        }
        if ($openingTime !== NULL && $closingTime !== NULL && $openingTime >= $closingTime) {
            throw new ChangeFacadeException('Čas zavření musí být větší než čas otevření.');
        }

        $newChange = new Change();
        $newChange->setDate($dateObject->format('Y-m-d'));
        $newChange->setOpeningTime($openingTime);
        $newChange->setClosingTime($closingTime);
        $this->changeRepo->save($newChange);

        return $newChange;
    }



    /**
     * @param $id int
     * @return bool
     */
    public function remove(int $id) : bool
    {
        try {
            $change = $this->changeRepo->getByMoreId([$id]);
            $this->changeRepo->remove(end($change));
            return TRUE;
        } catch (NotFoundException $exception) {
            //nothing..
        }
    }
}