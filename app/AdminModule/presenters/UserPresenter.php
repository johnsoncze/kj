<?php

namespace App\AdminModule\Presenters;


use App\Components\UserForm\UserForm;
use App\Components\UserForm\UserFormFactory;
use App\Components\UserList\UserList;
use App\Components\UserList\UserListFactory;
use App\FacadeException;
use App\NotFoundException;
use App\User\UserEntity;
use App\User\UserFacadeFactory;
use App\User\UserRepositoryFactory;
use Nette\Application\BadRequestException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @breadcrumb-nav-parent :Admin:Setting:default
 */
class UserPresenter extends AdminModulePresenter
{


    /** @var UserFacadeFactory @inject */
    public $userFacadeFactory;

    /** @var UserRepositoryFactory @inject */
    public $userRepositoryFactory;

    /** @var UserListFactory @inject */
    public $userListFactory;

    /** @var UserFormFactory @inject */
    public $userFormFactory;

    /** @var UserEntity */
    public $userEntity;



    /**
     * Render default
     * @return void
     */
    public function renderDefault()
    {
        $this->template->title = "Uživatelé";
    }



    /**
     * Render add
     * @return void
     */
    public function renderAdd()
    {
        $this->template->title = "Přidat uživatele";
    }



    /**
     * Action edit
     * @param $id int user id
     * @return void
     * @throws BadRequestException
     */
    public function actionEdit($id)
    {
        try {
            $user = $this->userRepositoryFactory
                ->create()
                ->getOneById($id);
            $this->userEntity = $user;
        } catch (NotFoundException $exception) {
            throw new BadRequestException(null, 404);
        }
        $this->template->setFile(__DIR__ . "/templates/User/add.latte");
        $this->template->title = "Upravit uživatele";
    }



    /**
     * Remove user
     * @param $id int
     * @return void
     */
    public function handleRemove($id)
    {
        try {
            $this->database->beginTransaction();
            $this->userFacadeFactory->create()->remove($id);
            $this->database->commit();
            $this->flashMessage("Uživatel byl smazán.", "success");
            $this->redirect("this");
        } catch (FacadeException $exception) {
            $this->database->rollBack();
            $this->flashMessage($exception->getMessage(), "danger");
        }
    }



    /**
     * User form
     * @return UserForm
     */
    public function createComponentUserForm()
    {
        $form = $this->userFormFactory->create();
        if ($this->userEntity) {
            $form->setUser($this->userEntity);
        }
        return $form;
    }



    /**
     * @return UserList
     */
    public function createComponentUserList()
    {
        return $this->userListFactory->create();
    }


}