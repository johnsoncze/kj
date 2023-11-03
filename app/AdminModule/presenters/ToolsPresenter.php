<?php

namespace App\AdminModule\Presenters;

use App\FrontModule\Components\Ecomail\EcomailHelper;
use Nette\Database\Context as NetteDatabase;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ToolsPresenter extends AdminModulePresenter
{

	public NetteDatabase $ndb;

	/** @var EcomailHelper @inject */
	public $ecomailHelper;

	public function __construct(NetteDatabase $ndb)
	{
		parent::__construct();
		$this->ndb = $ndb;
	}

	public function renderSalutation()
	{
		$a = $this->ndb->query("
            SELECT customer.cus_id, customer.cus_addressing, customer.cus_email, customer.cus_newsletter
            FROM customer
			WHERE customer.cus_id
			LIMIT ?
        ", 10)
			->fetchAll();

		foreach ($a as $record){
			echo $record->cus_email . '<br>';
		}

		echo 'SALs';
		exit;
	}

}