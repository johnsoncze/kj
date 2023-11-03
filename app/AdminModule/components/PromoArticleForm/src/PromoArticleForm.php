<?php

namespace App\Components\PromoArticleForm;


use App\PromoArticle\PromoArticleEntity;
use App\PromoArticle\PromoArticleFacadeException;
use App\PromoArticle\PromoArticleFacadeFactory;
use App\PromoArticle\PromoArticleRepository;
use App\PromoArticle\PromoArticleRepositoryFactory;
use App\Helpers\Entities;
use App\Helpers\Summernote;
use App\Libs\FileManager\Responses\DeleteFileResponse;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;




class PromoArticleForm extends Control
{

    /** @var Context */
    protected $database;

    /** @var PromoArticleFacadeFactory */
    protected $promoArticleFacadeFactory;

    /** @var PromoArticleRepositoryFactory */
    protected $promoArticleRepositoryFactory;

    /** @var PromoArticleEntity */
    protected $promoArticle;

		
    public function __construct(PromoArticleFacadeFactory $promoArticleFacadeFactory,
																PromoArticleRepositoryFactory $promoArticleRepositoryFactory,
                                Context $context)
    {
        parent::__construct();
        $this->promoArticleFacadeFactory = $promoArticleFacadeFactory;
        $this->promoArticleRepositoryFactory = $promoArticleRepositoryFactory;
        $this->database = $context;
    }


    /**
     * @param $promoArticle PromoArticle
     * @return self
     */
    public function setPromoArticle(PromoArticleEntity $promoArticle)
    {
        $this->promoArticle = $promoArticle;
        return $this;
    }
		
		

    /**
     * @return Form
     */
    public function createComponentForm()
    {
				$sequences = array();
				for ($i = 0; $i < 40; $i++) {
						$sequences[$i] = $i.".";
				}
			
        $form = new Form();
        $form->addText("title", "Název")
            ->setAttribute("class", "form-control")
            ->setAttribute('autofocus')
            ->setRequired("Zadejte název promo článku");
        $form->addText("urlText", "Text tlačítka")
            ->setAttribute("class", "form-control")
            ->setRequired("Zadejte text tlačítka");
        $form->addText("url", "Adresa tlačítka")
            ->setAttribute("class", "form-control")
            ->setRequired("Zadejte adresu tlačítka");
        $form->addUpload("photo", "Fotografie")
            ->setRequired(FALSE)
            ->addRule(Form::MAX_FILE_SIZE, "Maximální velikost souboru je 10 MB.", 10000000);
        $form->addCheckbox("isDefault", " Defaultní článek")
            ->setRequired(FALSE);
        $form->addTextArea("text", "Text")
            ->setAttribute("class", "form-control")
            ->setRequired("Vyplňte text promo článku.")
						->setHtmlId('ckEditor');
        $form->addSelect("sequence", "Pořadí", $sequences)
            ->setAttribute("class", "form-control");
        $form->addSubmit("submit", "Uložit")
            ->setAttribute("class", "btn btn-success");

				if ($this->promoArticle) {
						$form->onSuccess[] = [$this, "formSuccessEditPromoArticle"];
						
						$form->setDefaults([
								"title" => $this->promoArticle->getTitle(),
								"text" => $this->promoArticle->getText(),
								"url" => $this->promoArticle->getUrl(),
								"urlText" => $this->promoArticle->getUrlText(),
								"isDefault" => $this->promoArticle->getIsDefault(),
								"sequence" => $this->promoArticle->getSequence()
						]);								
				}
				else {
            $form->onSuccess[] = [$this, "formSuccessNewPromoArticle"];
					
				}
	
				
        return $form;
    }



    /**
     * @param $form Form
     * @return void
     */
    public function formSuccessNewPromoArticle(Form $form)
    {
        try {
            $values = $form->getValues();

            $this->database->beginTransaction();
            $article = $this->promoArticleFacadeFactory->create()->add(
                $values->title,
                $values->photo,
                $values->text,
                $values->url,
                $values->urlText,
                $values->sequence,
                $values->isDefault,
            );

            $this->database->commit();
            $this->presenter->flashMessage("Promo článek byl uložen.", "success");
            $this->presenter->redirect("PromoArticle:edit", ["id" => $article->getId()]);
        } catch (PromoArticleFacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        }
    }



    /**
     * @param Form $form
     */
    public function formSuccessEditPromoArticle(Form $form)
    {
        try {
            $values = $form->getValues();
						
						$repo = $this->promoArticleRepositoryFactory->create();
						
            $article = $this->promoArticle;
            $article->setTitle($values->title);
            if ($values->photo->hasFile()) {
                $article->setPhoto($values->photo);
            }
            $article->setUrl($values->url);
            $article->setUrlText($values->urlText);
            $article->setText($values->text);
            $article->setIsDefault($values->isDefault);
            $article->setSequence($values->sequence);
						
            $this->database->beginTransaction();
            $this->promoArticleFacadeFactory->create()->update($article);
            $this->database->commit();
            $this->presenter->flashMessage("Promo článek byl uložen.", "success");
            $this->presenter->redirect("this");
        } catch (PromoArticleFacadeException $exception) {
            $this->database->rollBack();
            $this->presenter->flashMessage($exception->getMessage(), "danger");
        }
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->setFile(__DIR__."/default.latte");
				if ($this->promoArticle) {
						$this->template->article = $this->promoArticle;
				}
        $this->template->render();
    }



    /**
     * @return void
     */
    public function handleRemovePhoto()
    {
        if ($this->presenter->isAjax()) {
            try {
                $this->database->beginTransaction();
                $article = $this->promoArticle;
                $article->setPhoto(NULL);
                $this->promoArticleFacadeFactory->create()->update($article);
                $this->database->commit();
                $response = new DeleteFileResponse('Fotografie byla smazána.', DeleteFileResponse::SUCCESS);
            } catch (ArticleFacadeException $exception) {
                $this->database->rollBack();
                $response = new DeleteFileResponse($exception->getMessage(), DeleteFileResponse::ERROR);
            }
            $this->presenter->sendJson($response->getResponseArray());
        }
    }
}