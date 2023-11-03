<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroup;

use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterCheckDuplicateException;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterEntity;
use App\Helpers\Entities;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationGroupParameterCheckDuplicate extends NObject
{


    /**
     * @param $productParameters array [int,..]
     * @param $groupParameters CategoryFiltrationGroupParameterEntity[]|null
     * @return CategoryFiltrationGroupParameterEntity[]|null
     * @throws CategoryFiltrationGroupParameterCheckDuplicateException
     */
    public function check(array $productParameters, array $groupParameters = NULL)
    {
        if ($groupParameters) {

            $parametersId = Entities::getProperty($productParameters, 'productParameterId');
            $duplicateParametersId = Entities::getProperty($groupParameters, 'productParameterId');

            if (Entities::getProperty($productParameters, 'categoryFiltrationGroupId')
                !== Entities::getProperty($groupParameters, 'categoryFiltrationGroupId')
                && count($parametersId) === count($duplicateParametersId)
                && (!array_diff($parametersId, $duplicateParametersId)
                    || !array_diff($duplicateParametersId, $parametersId))
            ) {
                throw new CategoryFiltrationGroupParameterCheckDuplicateException("Kombinace parametrů již existuje.");
            }
        }
        return $productParameters;
    }
}