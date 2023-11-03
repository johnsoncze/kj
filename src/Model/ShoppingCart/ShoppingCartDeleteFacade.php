<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

use Kdyby\Translation\ITranslator;
use Nette\Http\Session;
use Nette\InvalidArgumentException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ShoppingCartDeleteFacade
{


    /** @var Session */
    protected $session;

    /** @var ShoppingCartRepositoryFactory */
    protected $shoppingCartRepoFactory;

    /** @var ITranslator */
    protected $translator;



    public function __construct(Session $session,
                                ShoppingCartRepositoryFactory $shoppingCartRepoFactory,
                                ITranslator $translator)
    {
        $this->session = $session;
        $this->shoppingCartRepoFactory = $shoppingCartRepoFactory;
        $this->translator = $translator;
    }



    /**
     * @param int $id
     * @return bool
     * @throws ShoppingCartDeleteFacadeException
     * @throws InvalidArgumentException
     */
    public function delete(int $id) : bool
    {
        try {
            $shoppingCartRepo = $this->shoppingCartRepoFactory->create();
            $cart = $shoppingCartRepo->getOneById($id, $this->translator);
            $shoppingCartRepo->remove($cart);
            $this->session->getSection(ShoppingCart::SESSION_SECTION)->remove();
            return TRUE;
        } catch (ShoppingCartNotFoundException $exception) {
            throw new ShoppingCartDeleteFacadeException($this->translator->translate(sprintf('%s.deleted.already', ShoppingCartTranslation::getFileName())));
        }
    }
}