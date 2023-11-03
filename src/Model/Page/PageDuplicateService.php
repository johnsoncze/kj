<?php

namespace App\Page;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageDuplicateService extends NObject
{


    /**
     * @param PageEntity $pageEntity
     * @param PageEntity|NULL $duplicateEntity
     * @return PageEntity
     * @throws PageDuplicateServiceException
     */
    public function checkName(PageEntity $pageEntity, PageEntity $duplicateEntity = NULL)
    {
        if ($duplicateEntity instanceof PageEntity && (int)$pageEntity->getId() !== (int)$duplicateEntity->getId() && $pageEntity->getName() === $duplicateEntity->getName()) {
            throw new PageDuplicateServiceException("Stránka s názvem '{$pageEntity->getName()}' již existuje.");
        }
        return $pageEntity;
    }



    /**
     * @param PageEntity $pageEntity
     * @param PageEntity|NULL $duplicateEntity
     * @return PageEntity
     * @throws PageDuplicateServiceException
     */
    public function checkUrl(PageEntity $pageEntity, PageEntity $duplicateEntity = NULL)
    {
        if ($duplicateEntity instanceof PageEntity && $pageEntity->getId() != $duplicateEntity->getId() && $pageEntity->getUrl() == $duplicateEntity->getUrl()) {
            throw new PageDuplicateServiceException("Stránka s url adresou '{$pageEntity->getUrl()}' již existuje.");
        }
        return $pageEntity;
    }

}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageDuplicateServiceException extends \Exception
{


}