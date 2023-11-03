<?php

declare(strict_types = 1);

namespace App\Opportunity\Email;

use App\Opportunity\OpportunityNotFoundException;
use App\Opportunity\OpportunityRepository;
use App\Opportunity\Product\ProductRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SendEmailFacade
{


    /** @var EmailSender */
    private $emailSender;

    /** @var OpportunityRepository */
    private $opportunityRepo;

    /** @var ProductRepository */
    private $productRepo;



    public function __construct(EmailSender $emailSender,
                                OpportunityRepository $opportunityRepository,
                                ProductRepository $productRepository)
    {
        $this->emailSender = $emailSender;
        $this->opportunityRepo = $opportunityRepository;
        $this->productRepo = $productRepository;
    }



    /**
     * Send confirm.
     * @param $opportunityId int
     * @return void
     */
    public function sendConfirm(int $opportunityId)
    {
        try {
            $opportunity = $this->opportunityRepo->getOneById($opportunityId);
            if (true) {
                $products = $this->productRepo->findByOpportunityId((int)$opportunity->getId());
                $this->emailSender->send($opportunity, $products);
            }
        } catch (OpportunityNotFoundException $exception) {
            //nothing..
        }
    }
}
