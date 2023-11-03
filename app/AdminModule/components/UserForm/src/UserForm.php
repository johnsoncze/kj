<?php

namespace App\Components\UserForm;

use App\FacadeException;
use App\User\UserEntity;
use App\User\UserFacadeFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class UserForm extends Control
{


    /** @var Context */
    protected $database;

    /** @var UserFacadeFactory */
    protected $userFacadeFactory;

    /** @var UserEntity */
    protected $user;



    public function __construct(Context $context, UserFacadeFactory $userFacadeFactory)
    {
        parent::__construct();
        $this->database = $context;
        $this->userFacadeFactory = $userFacadeFactory;
    }



    /**
     * @param $userEntity UserEntity
     * @return self
     */
    public function setUser(UserEntity $userEntity)
    {
        $this->user = $userEntity;
        return $this;
    }



    /**
     * @return Form
     */
    public function createComponentUserForm()
    {
        $form = new Form();
        $form->addText("name", "Jméno*")
            ->setRequired("Vyplňte jméno.")
            ->setAttribute("class", "form-control")
            ->setAttribute('autofocus');
        $form->addText("email", "E-mail*")
            ->setRequired("Vyplňte e-mail.")
            ->setAttribute("class", "form-control");
        $form->addPassword("password", "Heslo*")
            ->setAttribute("class", "form-control")
            ->addConditionOn($form["password"], Form::FILLED, true)
            ->addRule(Form::MIN_LENGTH, "Minimální délka hesla musí být " . UserEntity::PASSWORD_MIN_LENGTH . " znaků.", UserEntity::PASSWORD_MIN_LENGTH);
        $form->addPassword("passwordConfirm", "Potvrzení hesla*")
            ->setAttribute("class", "form-control")
            ->addConditionOn($form["password"], Form::FILLED, true)
            ->setRequired("Potvrďte heslo.")
            ->addRule(Form::EQUAL, "Hesla nejsou stejná.", $form["password"]);
        $form->addSelect('role', 'Role*', UserEntity::getRoleList())
			->setPrompt('- vyberte -')
			->setAttribute("class", "form-control")
			->setRequired('Vyberte roli.');
        $form->addSubmit("submit", "Uložit")
            ->setAttribute("class", "btn btn-primary");
        if ($this->user) {
            $form->setDefaults([
                "name" => $this->user->getName(),
                "email" => $this->user->getEmail(),
				'role' => $this->user->getRole(),
            ]);
            $form["password"]->setRequired(false);
        } else {
            $form["password"]->setRequired("Zvolte heslo.");
            $form["passwordConfirm"]->setRequired("Potvrďte heslo.");
        }
        $form->onSuccess[] = [$this, "userFormSuccess"];
        return $form;
    }



    /**
     * User form success process
     * @return void
     */
    public function userFormSuccess(Form $form)
    {
        $values = $form->getValues();
        try {
            $this->database->beginTransaction();
            if ($this->user) {
                $this->user->setName($values->name);
                $this->user->setEmail($values->email);
                $this->user->setRole($values->role);
                $this->userFacadeFactory->create()->save($this->user, ($values->password ? $values->password : null));
                $userEntity = $this->user;
            } else {
                //New user
                $userEntity = $this->userFacadeFactory->create()->addNew($values->name, $values->email, $values->password, $values->role);
            }
            $this->database->commit();
            $this->presenter->flashMessage("Uživatel s e-mailovou adresou '{$values->email}' byl uložen.", "success");
            $this->presenter->redirect("User:edit", [
                "id" => $userEntity->getId()
            ]);
        } catch (FacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        }
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