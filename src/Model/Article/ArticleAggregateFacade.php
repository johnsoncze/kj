<?php
/**
 * Created by PhpStorm.
 * User: dusanmlynarcik
 * Date: 02.01.17
 * Time: 23:14
 */
declare(strict_types = 1);


namespace App\Article;

use App\ArticleCategory\ArticleCategoryRepositoryFactory;
use App\ArticleCategoryRelationship\ArticleCategoryRelationshipRepositoryFactory;
use App\Helpers\Entities;
use App\NotFoundException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleAggregateFacade
{


    /**
     * @var ArticleRepositoryFactory
     */
    protected $articleRepositoryFactory;
    /**
     * @var ArticleCategoryRelationshipRepositoryFactory
     */
    protected $articleCategoryRelationshipRepositoryFactory;
    /**
     * @var ArticleCategoryRepositoryFactory
     */
    protected $articleCategoryRepositoryFactory;



    public function __construct(ArticleRepositoryFactory $articleRepositoryFactory,
                                ArticleCategoryRelationshipRepositoryFactory $articleCategoryRelationshipRepositoryFactory,
                                ArticleCategoryRepositoryFactory $articleCategoryRepositoryFactory)
    {
        $this->articleRepositoryFactory = $articleRepositoryFactory;
        $this->articleCategoryRelationshipRepositoryFactory = $articleCategoryRelationshipRepositoryFactory;
        $this->articleCategoryRepositoryFactory = $articleCategoryRepositoryFactory;
    }



    /**
     * @param $articleId int
     * @param $publish bool
     * @return ArticleAggregate
     * @throws ArticleAggregatorFacadeException
     */
    public function create(int $articleId, $publish = TRUE) : ArticleAggregate
    {
        $articleRepo = $this->articleRepositoryFactory->create();

        try {
            $article = $publish === TRUE ? $articleRepo->getOnePublishedById($articleId) : $articleRepo->getOneById($articleId);
            $cat = $this->articleCategoryRelationshipRepositoryFactory->create()->findByArticleId($articleId);
            if ($cat) {
                $id = Entities::getProperty($cat, "articleCategoryId");
                $categories = $this->articleCategoryRepositoryFactory->create()->findByMoreId($id);
            }
            $agg = new ArticleAggregate($article);
            foreach (isset($categories) && $categories !== null ? $categories : [] as $category) {
                $agg->addCategory($category);
            }
            return $agg;
        } catch (NotFoundException $exception) {
            throw new ArticleAggregatorFacadeException($exception->getMessage());
        }
    }
}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleAggregatorFacadeException extends \Exception
{


}