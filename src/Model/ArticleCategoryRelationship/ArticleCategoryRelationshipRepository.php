<?php

declare(strict_types = 1);

namespace App\ArticleCategoryRelationship;

use App\NotFoundException;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Exceptions\RepositoryException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCategoryRelationshipRepository extends BaseRepository
{


    /** @var string */
    protected $entityName = ArticleCategoryRelationshipEntity::class;



    /**
     * @param int $id
     * @return IEntity|null
     * @throws NotFoundException
     */
    public function getOneById(int $id)
    {
        if ($id) {
            $result = $this->findOneBy([
                "where" => [
                    ["id", "=", $id]
                ]
            ]);
            if ($result) {
                return $result;
            }
        }
        throw new NotFoundException("Rubrika nenalezena.");
    }



    /**
     * @param $id int
     * @return ArticleCategoryRelationshipEntity[]|null
     */
    public function findByArticleCategoryId($id)
    {
        if ($id) {
            $result = $this->findBy([
                "where" => [
                    ["articleCategoryId", "=", $id]
                ]
            ]);
            if ($result) {
                return $result;
            }
        }
        return null;
    }



    /**
     * @param $articleId
     * @return IEntity[]|null
     */
    public function findByArticleId(int $articleId)
    {
        if ($articleId) {
            $result = $this->findBy([
                "where" => [
                    ["articleId", "=", $articleId]
                ]
            ]);
            if ($result) {
                return $result;
            }
        }
        return null;
    }



    /**
     * @param $id int
     * @return CountDTO
     * @throws RepositoryException
     */
    public function getCategoryArticlesCount($id)
    {
        if (!$id) {
            throw new RepositoryException("Missing category id.");
        }
        return $this->count([
            "where" => [
                ["articleCategoryId", "=", $id]
            ]
        ]);
    }
}