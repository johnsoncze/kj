<?php

declare(strict_types = 1);

namespace App\Page;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use App\NotFoundException;
use App\Url\IUrlRepository;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageRepository extends BaseRepository implements IRepository, IRepositorySource, IUrlRepository
{


    /** @var string */
    protected $entityName = PageEntity::class;



    /**
     * @param $id array
     * @return PageEntity[]|array
     */
    public function findByMoreId(array $id) : array
    {
        $filter['where'][] = ['id', '', $id];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $id array
     * @return PageEntity[]|array
    */
    public function findPublishedByMoreModuleId(array $id) : array
    {
        $filter['where'][] = ['articleModuleId', '', $id];
        $filter['where'][] = $this->getPublishCondition(TRUE);
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $languageId int
     * @return PageEntity[]|array
    */
    public function findPublishedByLanguageId(int $languageId) : array
    {
        $filter['sort'] = ['name', 'ASC'];
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = $this->getPublishCondition(TRUE);
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $id array
     * @return PageEntity[]|array
    */
    public function findPublishedByMoreParentId(array $id) : array
    {
		$filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        $filter['where'][] = ['parentPageId', '', $id];
        $filter['where'][] = $this->getPublishCondition(TRUE);
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $langId int
     * @param bool $publish
     * @return null|PageEntity
     */
    public function findByLangIdWithoutPageId(int $langId, int $pageId = NULL, bool $publish = TRUE)
    {
        $where[] = ["languageId", "=", $langId];
        if ($pageId) {

            $annotation = PageEntity::getAnnotation();
            $parentPageId = $annotation->getPropertyByName("parentPageId");

            $where[] = ["id", "!=", $pageId];
            $where[] = ["{$parentPageId->getColumn()->getName()} ? OR {$parentPageId->getColumn()->getName()} NOT ? && {$parentPageId->getColumn()->getName()} != ?", [NULL, NULL, $pageId]];
        }
        if ($publish === TRUE) {
            $where[] = $this->getPublishCondition($publish);
        }
        $result = $this->findBy([
            "where" => $where,
            "sort" => [['LENGTH(name)', 'name'], "ASC"]
        ]);
        if ($result) {
            return $result;
        }
    }



    /**
     * @param $name
     * @param $langId
     * @return null|PageEntity
     */
    public function findOneByNameAndLangId($name, $langId)
    {
        if ($name && $langId) {
            return $this->findOneBy([
                "where" => [
                    ["name", "=", $name],
                    ["languageId", "=", $langId]
                ]
            ]);
        }
        return null;
    }



    /**
     * @param $url
     * @param $langId
     * @return null|PageEntity
     */
    public function findOneByUrlAndLangId($url, $langId)
    {
        if ($url && $langId) {
            return $this->findOneBy([
                "where" => [
                    ["url", "=", $url],
                    ["languageId", "=", $langId]
                ]
            ]);
        }
        return null;
    }



    /**
     * @param $url string
     * @param $languageId int
     * @return PageEntity|null
    */
    public function findOneByUrlAndLanguageId(string $url, int $languageId)
    {
        return $this->findOneByUrlAndLangId($url, $languageId) ?: NULL;
    }



    /**
     * @param int $langId
     * @return PageEntity[]|null
     */
    public function findByLangId(int $langId, bool $publish = TRUE)
    {
        $where[] = ["languageId", "=", $langId];

        if ($publish) {
            $where[] = $this->getPublishCondition($publish);
        }

        return $this->findBy([
            "where" => $where,
            'sort' => ['name', 'ASC'],
        ]);
    }



    /**
     * @param $url
     * @return PageEntity
     * @throws NotFoundException
     */
    public function getOneByUrl($url, $publish = TRUE)
    {
        if ($url) {
            $where[] = ["url", "=", $url];
            if ($publish === TRUE) {
                $where[] = $this->getPublishCondition($publish);
            }
            $result = $this->findOneBy([
                "where" => $where
            ]);
            if ($result) {
                return $result;
            }
        }
        throw new NotFoundException("Stránka nebyla nalezena.");
    }



    /**
     * @param $url string
     * @param $languageId int
     * @param $type string
     * @return PageEntity
     * @throws NotFoundException
     */
    public function getOnePublishedByUrlAndLanguageIdAndType(string $url, int $languageId, string $type = PageEntity::TEXT_TYPE) : PageEntity
    {
        $filter['where'][] = ['url', '=', $url];
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = $this->getPublishCondition(TRUE);
        $filter['where'][] = ['type', '=', $type];
        $result = $this->findOneBy($filter);
        if (!$result) {
            throw new NotFoundException('Stránka nebyla nalezena.');
        }
        return $result;
    }



    /**
     * @param $id int
     * @return PageEntity
     * @throws NotFoundException
     */
    public function getOneById($id, $publish = TRUE)
    {
        if ($id) {
            $where[] = ["id", "=", $id];
            if ($publish === TRUE) {
                $where[] = $this->getPublishCondition($publish);
            }
            $result = $this->findOneBy([
                "where" => $where
            ]);
            if ($result) {
                return $result;
            }
        }
        throw new NotFoundException("Stránka nebyla nalezena.");
    }



    /**
     * @param $languageId int
     * @param $menuLocation int
     * @return PageEntity[]|array
     */
    public function findPublishedParentsByLanguageIdAndMenuLocation(int $languageId, int $menuLocation) : array
    {
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['menuLocation', '=', $menuLocation];
        $filter['where'][] = ['parentPageId', '', NULL];
        $filter['where'][] = $this->getPublishCondition(TRUE);
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $languageId int
     * @param $menuLocation int
     * @return PageEntity[]|array
     */
    public function findPublishedByLanguageIdAndMenu(int $languageId, int $menuLocation) : array
    {
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['menuLocation', '=', $menuLocation];
        $filter['where'][] = $this->getPublishCondition(TRUE);
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $moduleId int
     * @return PageEntity|null
    */
    public function findOneArticlesTypeByArticleModuleId(int $moduleId)
    {
        $filter['where'][] = ['articleModuleId', '=', $moduleId];
        $filter['where'][] = ['type', '=', PageEntity::ARTICLES_TYPE];
        return $this->findOneBy($filter) ?: NULL;
    }



    /**
     * @param $moduleId array
     * @return PageEntity[]|array
     */
    public function findArticlesTypeByMoreArticleModuleId(array $moduleId) : array
    {
        $filter['where'][] = ['articleModuleId', '', $moduleId];
        $filter['where'][] = ['type', '=', PageEntity::ARTICLES_TYPE];
        return $this->findBy($filter) ?: [];
    }



    /**
	 * @param $languageId int
	 * @return PageEntity[]|array
    */
	public function findPublishedWithoutParentIdByLanguageId(int $languageId) : array
	{
		$filter['where'][] = ['parentPageId', '', NULL];
		$filter['where'][] = ['languageId', '=', $languageId];
		$filter['where'][] = $this->getPublishCondition(TRUE);
		$filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
		return $this->findBy($filter) ?: [];
	}



	/**
	 * @param $languageId int
	 * @param $menuId int
	 * @return PageEntity[]|array
	 */
	public function findPublishedWithoutParentIdByLanguageIdAndMenuLocation(int $languageId, int $menuId) : array
	{
		$filter['where'][] = ['parentPageId', '', NULL];
		$filter['where'][] = ['menuLocation', '=', $menuId];
		$filter['where'][] = ['languageId', '=', $languageId];
		$filter['where'][] = $this->getPublishCondition(TRUE);
		$filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
		return $this->findBy($filter) ?: [];
	}



    /**
     * @param $publish bool
     * @return array
     */
    protected function getPublishCondition($publish)
    {
        return $publish === TRUE ? ["status", "=", PageEntity::PUBLISH] : [];
    }
}