<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\CustomerList\CustomerList;
use App\AdminModule\Components\CustomerList\CustomerListFactory;
use App\Customer\Customer;
use App\Customer\CustomerRepositoryFactory;
use App\Customer\CustomerStorageFacadeFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CustomerPresenter extends AdminModulePresenter
{

    /** @var CustomerStorageFacadeFactory @inject */
    public $customerStorageFacadeFactory;


    /** @var CustomerListFactory @inject */
    public $customerListFactory;

    /** @var Customer|null */
    private $customer;



    /**
     * Handler for 'detail' action.
     * @param $id int id of customer
     * @return void
     */
    public function actionDetail(int $id)
    {
        $this->customer = $this->checkRequest($id, CustomerRepositoryFactory::class);

        $this->template->customer = $this->customer;
    }



    public function renderDetail()
    {
        $this->addToHeadline($this->customer->getFullName());
    }



    /**
     * @return CustomerList
     */
    public function createComponentCustomerList() : CustomerList
    {
        return $this->customerListFactory->create();
    }
		
		
    /**
     * @return void
     */
    public function handleSetBirthdayDiscountUse(int $id)
    {
        if ($this->presenter->isAjax()) {
            try {
                $this->database->beginTransaction();
                $this->customerStorageFacadeFactory->create()->setBirthdayDiscountUse($id);
                $this->database->commit();
								$response = [
										'message' => "Sleva byla vyčerpána."
								];
            } catch (CustomerBirthDayDiscountException $exception) {
                $this->database->rollBack();
								$response = [
										'message' => "Chyba: ".$exception->getMessage()
								];
            }

						$this->presenter->sendJson($response);
							
       }
    }
		
}


class CustomerBirthDayDiscountException extends \Exception
{
}