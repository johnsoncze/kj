<?php

declare(strict_types = 1);

namespace App\Payment;

use Kdyby\Translation\ITranslator;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\Repositories\Traits\ReadOnlyTrait;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PaymentAllowedRepository extends BaseRepository
{


    use ReadOnlyTrait;

    /** @var string */
    protected $entityName = Payment::class;



    /**
     * @param int $id
     * @param ITranslator $translator
     * @return Payment
     * @throws PaymentNotFoundException
     */
    public function getOneById(int $id, ITranslator $translator) : Payment
    {
        $payment = $this->findOneBy([
            'where' => [
                ['id', '=', $id],
                $this->getCondition()
            ]
        ]);
        if (!$payment) {
            throw new PaymentNotFoundException($translator->translate(sprintf('%s.not.found', Helpers::getTranslationFileKey())));
        }
        return $payment;
    }



    /**
     * @return Payment[]|array
     */
    public function findAll() : array
    {
        $filter['sort'] = [['sort', 'LENGTH(sort)'], 'ASC'];
        $filter['where'][] = $this->getCondition();
        return $this->findBy($filter) ?: [];
    }



    /**
     * @return Payment[]|array
    */
    public function findAvailableForNonStockProducibleProduct() : array
    {
        $filter['sort'] = [['sort', 'LENGTH(sort)'], 'ASC'];
        $filter['where'][] = $this->getCondition();
        $filter['where'][] = ['nonStockProducibleProductAvailability', '=', TRUE];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @return array
     */
    protected function getCondition() : array
    {
        return ['state', '=', Payment::ALLOWED];
    }
}