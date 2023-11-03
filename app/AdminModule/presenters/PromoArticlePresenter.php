<?php

namespace App\AdminModule\Presenters;


use App\Components\PromoArticleForm\PromoArticleForm;
use App\Components\PromoArticleForm\PromoArticleFormFactory;
use App\Components\PromoArticleList\PromoArticleList;
use App\Components\PromoArticleList\PromoArticleListFactory;
use App\PromoArticle\PromoArticleRepository;
use App\PromoArticle\PromoArticleRepositoryFactory;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;




class PromoArticlePresenter extends AdminModulePresenter
{

    /** @var PromoArticleListFactory @inject */
    public $promoArticleListFactory;

    /** @var PromoArticleFormFactory @inject */
    public $promoArticleFormFactory;

    /** @var PromoArticleRepositoryFactory @inject */
    public $promoArticleRepositoryFactory;

    public $promoArticle;
		

    /**
     * @return void
     */
    public function actionAdd()
    {
				$this->template->setFile(__DIR__ . "/templates/PromoArticle/add.latte");
    }



    /**
     * @param $id
     * @throws BadRequestException
     */
    public function actionEdit($id)
    {
				$promoArticleRepository = $this->promoArticleRepositoryFactory->create();
				$this->promoArticle = $promoArticleRepository->getOneById($id);
        if (!$this->promoArticle) {
            throw new BadRequestException(null, 404);
        }
        $this->template->setFile(__DIR__ . "/templates/PromoArticle/add.latte");
    }


    /**
     * @return PromoArticleForm
     */
    public function createComponentPromoArticleForm()
    {
        $form = $this->promoArticleFormFactory->create();
				if ($this->promoArticle) {
						$form->setPromoArticle($this->promoArticle);
				}
        return $form;
    }


    /**
     * @return PromoArticleList
     */
    public function createComponentPromoArticleList()
    {
        return $this->promoArticleListFactory->create();
    }
}