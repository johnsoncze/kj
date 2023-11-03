<?php

declare(strict_types = 1);

namespace App\Tests\Opportunity;

require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

use App\Opportunity\Opportunity;
use App\Opportunity\OpportunityRepository;
use App\Opportunity\OpportunityStorageFacade;
use App\Opportunity\OpportunityStorageFacadeFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OpportunityStorageFacadeTest extends BaseTestCase
{


    use OpportunityTestTrait;

    /** @var OpportunityRepository */
    private $opportunityRepo;

    /** @var OpportunityStorageFacade */
    private $opportunityStorageFacade;



    protected function setUp()
    {
        parent::setUp();
        $this->opportunityRepo = $this->container->getByType(OpportunityRepository::class);
        $opportunityStorageFacadeFactory = $this->container->getByType(OpportunityStorageFacadeFactory::class);
        $this->opportunityStorageFacade = $opportunityStorageFacadeFactory->create();
    }



    public function testAddSuccess()
    {
        $testOpportunity = $this->createTestOpportunity();

        $response = $this->opportunityStorageFacade->add($testOpportunity->getCustomerId(),
            $testOpportunity->getFirstName(), $testOpportunity->getLastName(), $testOpportunity->getPreferredContact(),
            $testOpportunity->getEmail(), $testOpportunity->getTelephone(), $testOpportunity->getComment(),
            $testOpportunity->getPage(), $testOpportunity->getPageId(), $testOpportunity->getType());
        $opportunityFromStorage = $this->opportunityRepo->getOneById($response->getId());
        $this->addEntityForRemove($opportunityFromStorage, $this->opportunityRepo);

        Assert::type(Opportunity::class, $response);
        Assert::type(Opportunity::class, $opportunityFromStorage);

        /** @var $opportunity Opportunity */
        foreach ([$response, $opportunityFromStorage] as $opportunity) {
            Assert::null($opportunity->getCustomerId());
            Assert::true(strlen($opportunity->getCode()) === 10);
            Assert::same($testOpportunity->getFirstName(), $opportunity->getFirstName());
            Assert::same($testOpportunity->getLastName(), $opportunity->getLastName());
            Assert::same($testOpportunity->getPreferredContact(), $opportunity->getPreferredContact());
            Assert::same($testOpportunity->getEmail(), $opportunity->getEmail());
            Assert::same($testOpportunity->getTelephone(), $opportunity->getTelephone());
            Assert::same($testOpportunity->getComment(), $opportunity->getComment());
            Assert::same($testOpportunity->getPage(), $opportunity->getPage());
            Assert::same($testOpportunity->getPageId(), (int)$opportunity->getPageId());
            Assert::same($testOpportunity->getType(), $opportunity->getType());
        }
    }
}

(new OpportunityStorageFacadeTest())->run();