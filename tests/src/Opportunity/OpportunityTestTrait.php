<?php

declare(strict_types = 1);

namespace App\Tests\Opportunity;

use App\Opportunity\Opportunity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait OpportunityTestTrait
{


    /**
     * Create object with dummy data.
     * @return Opportunity
     */
    public function createTestOpportunity() : Opportunity
    {
        $opportunity = new Opportunity();
        $opportunity->setCode('DE1234567890');
        $opportunity->setCustomerId(1);
        $opportunity->setFirstName('John');
        $opportunity->setLastName('Doe');
        $opportunity->setPreferredContact(Opportunity::PREFERRED_CONTACT_TELEPHONE);
        $opportunity->setEmail('johndoe@jk.cz');
        $opportunity->setTelephone('+420777666555');
        $opportunity->setComment('Some comment.');
        $opportunity->setPage('Front:Product:detail');
        $opportunity->setPageId(123);
        $opportunity->setState(Opportunity::STATE_NEW);
        $opportunity->setType(Opportunity::TYPE_STORE_MEETING);

        return $opportunity;
    }
}