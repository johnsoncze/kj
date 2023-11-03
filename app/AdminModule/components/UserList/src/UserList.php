<?php

namespace App\Components\UserList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\User\UserEntity;
use App\User\UserRepositoryFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class UserList extends GridoComponent
{


    public function __construct(GridoFactory $gridoFactory, UserRepositoryFactory $userRepositoryFactory)
    {
    	parent::__construct($gridoFactory);
        $this->repositoryFactory = $userRepositoryFactory;
    }



    public function createComponentUserList()
    {
        $grido = $this->gridoFactory->create();
        $grido->setModel((new RepositorySource($this->repositoryFactory->create())));
        $grido->addColumnText("name", "Jméno")
            ->setSortable()
            ->setFilterText();
        $grido->addColumnText("email", "E-mail")
            ->setSortable()
            ->setFilterText();
        $grido->addColumnText('role', 'Role')
			->setCustomRender(function(UserEntity $user) {
				return $user->getTranslatedRole();
			})
			->setSortable()
			->setFilterSelect(Arrays::mergeTree(['' => ''], UserEntity::getRoleList()));
        $grido->addColumnDate("addDate", "Přidáno")
            ->setDateFormat("d.m.Y H:i:s");
        $grido->addActionHref("edit", "", "User:edit")
            ->setIcon("pencil");
        $grido->addActionHref("remove", "", "remove!")
            ->setIcon("trash")
            ->setConfirm(function($row){
                return 'Opravdu si přejete smazat uživatele s e-mailem ' . $row["email"] . '?';
            });
        $grido->getColumn("name")->getHeaderPrototype()->style["width"] = "20%";
        $grido->getColumn("email")->getHeaderPrototype()->style["width"] = "20%";
        $grido->getColumn("addDate")->getHeaderPrototype()->style["width"] = "20%";
        return $grido;
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }
}