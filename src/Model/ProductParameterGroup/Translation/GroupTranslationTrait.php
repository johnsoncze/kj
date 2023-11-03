<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup\Translation;

use App\Helpers\Entities;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepository;
use Ricaefeliz\Mappero\Translation\Localization;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait GroupTranslationTrait
{


    /**
     * Get group list.
     * @param $repo ProductParameterGroupTranslationRepository
     * @param $localization Localization
     * @return array
     */
    public function getGroupList(ProductParameterGroupTranslationRepository $repo, Localization $localization) : array
    {
        $groups = $repo->findByLanguageId($localization->getId());
        return $groups ? Entities::toPair($groups, 'productParameterGroupId', 'name') : [];
    }
}