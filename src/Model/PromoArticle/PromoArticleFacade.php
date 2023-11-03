<?php

declare(strict_types = 1);

namespace App\PromoArticle;


use App\Helpers\Entities;
use App\NotFoundException;
use App\Page\PageEntity;
use App\Page\PageRepository;
use Nette\Http\FileUpload;
use App\NObject;



class PromoArticleFacade extends NObject
{

    /** @var PromoArticleRepositoryFactory */
    protected $promoArticleRepositoryFactory;

    /** @var PromoArticleCoverPhotoServiceFactory */
    protected $promoArticleCoverPhotoServiceFactory;

		


    public function __construct(PromoArticleRepositoryFactory $promoArticleRepositoryFactory,
                                PromoArticleCoverPhotoServiceFactory $promoArticleCoverPhotoServiceFactory)
    {
        $this->promoArticleRepositoryFactory = $promoArticleRepositoryFactory;
        $this->promoArticleCoverPhotoServiceFactory = $promoArticleCoverPhotoServiceFactory;
    }



    /**
     * @param $title string
     * @param $photo null|FileUpload
     * @param $text string
     * @param $url string
     * @param $urlText string
     * @param $sequence int
     * @param $isDefault int		 
     * @return PromoArticleEntity
     * @throws PromoArticleFacadeException
     */
    public function add($title,
                        $photo = null,
                        $text,
                        $url = null,
                        $urlText = null,
                        $sequence = 0,
                        $isDefault = 0
    )
    {
        try {
            if ($photo instanceof FileUpload && $photo->hasFile()) {
                $photo = $this->promoArticleCoverPhotoServiceFactory->create()->upload($photo);
            }


						$createService = new PromoArticleCreateService();
            $entity = $createService->createEntity($title, $photo, $text, $url,
																								   $urlText, $sequence, $isDefault);
            $repo = $this->promoArticleRepositoryFactory->create();
            $repo->save($entity);

            return $entity;
        } catch (NotFoundException $exception) {
            throw new PromoArticleFacadeException($exception->getMessage());
        } catch (PromoArticleDuplicateServiceException $exception) {
            throw new PromoArticleFacadeException($exception->getMessage());
        } catch (PromoArticleCoverPhotoServiceException $exception) {
            throw new PromoArticleFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $entity PromoArticleEntity
     * @return PromoArticleEntity
     * @throws PromoArticleFacadeException
     */
    public function update(PromoArticleEntity $entity)
    {
        if (!$entity->getId()) {
            throw new ArticleFacadeException("For a new promo article use 'add()' method.");
        }
        try {
            if ($entity->getPhoto() instanceof FileUpload && $entity->getPhoto()->hasFile()) {
                $entity->setPhoto($this->promoArticleCoverPhotoServiceFactory->create()->upload($entity->getPhoto()));
            }
            $repo = $this->promoArticleRepositoryFactory->create();
            $repo->save($entity);

            return $entity;
        } catch (PromoArticleDuplicateServiceException $exception) {
            throw new PromoArticleFacadeException($exception->getMessage());
        } catch (PromoArticleCoverPhotoServiceException $exception) {
            throw new PromoArticleFacadeException($exception->getMessage());
        } catch (NotFoundException $exception) {
            throw new PromoArticleFacadeException($exception->getMessage());
        }
    }


    /**
     * @param $id int
     * @return int
     * @throws PromoArticleFacadeException
     */
    public function remove(int $id) : int
    {
        try {
            $repo = $this->promoArticleRepositoryFactory->create();
            $article = $repo->getOneById($id);
            return $repo->remove($article);
        } catch (NotFoundException $exception) {
            throw new PromoArticleFacadeException($exception->getMessage());
        }
    }


}


class PromoArticleFacadeException extends \Exception
{


}