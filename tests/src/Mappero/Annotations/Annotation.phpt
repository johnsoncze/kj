<?php

namespace App\Tests\Mappero\Annotations;

require_once __DIR__ . "/../../bootstrap.php";

use App\Article\ArticleEntity;
use App\Tests\BaseTestCase;
use Ricaefeliz\Mappero\Annotations\Relation;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Annotation extends BaseTestCase
{


    public function testGetAnnotationFromArticleEntity()
    {
        $annotation = ArticleEntity::getAnnotation();

        //Type of class
        Assert::type(\Ricaefeliz\Mappero\Annotations\Annotation::class, $annotation);
        Assert::same(ArticleEntity::class, $annotation->getEntityName());

        //Table
        Assert::same("article", $annotation->getTable()->getName());

        //Primary column
        Assert::same("id", $annotation->getPrimaryProperty()->getName());
        Assert::same("art_id", $annotation->getPrimaryProperty()->getColumn()->getName());

        //Properties
        $properties = $annotation->getProperties();
        Assert::same(13, count($properties));

        Assert::same("id", $properties[0]->getName());
        Assert::same("art_id", $properties[0]->getColumn()->getName());

        Assert::same("languageId", $properties[1]->getName());
        Assert::same("art_language_id", $properties[1]->getColumn()->getName());

        Assert::same("name", $properties[2]->getName());
        Assert::same("art_name", $properties[2]->getColumn()->getName());

        Assert::same("url", $properties[3]->getName());
        Assert::same("art_url", $properties[3]->getColumn()->getName());

        Assert::same("titleSeo", $properties[4]->getName());
        Assert::same("art_title_seo", $properties[4]->getColumn()->getName());

        Assert::same("descriptionSeo", $properties[5]->getName());
        Assert::same("art_description_seo", $properties[5]->getColumn()->getName());

        Assert::same("coverPhoto", $properties[6]->getName());
        Assert::same("art_cover_photo", $properties[6]->getColumn()->getName());

        Assert::same("introduction", $properties[7]->getName());
        Assert::same("art_introduction", $properties[7]->getColumn()->getName());

        Assert::same("content", $properties[8]->getName());
        Assert::same("art_content", $properties[8]->getColumn()->getName());

        Assert::same("status", $properties[9]->getName());
        Assert::same("art_status", $properties[9]->getColumn()->getName());

        Assert::same("addDate", $properties[10]->getName());
        Assert::same("art_add_date", $properties[10]->getColumn()->getName());

        Assert::same("categories", $properties[11]->getName());
        Assert::same(NULL, $properties[11]->getColumn());
        Assert::type(Relation::class, $properties[11]->getRelation());
        Assert::same(Relation::ONE_TO_MANY, $properties[11]->getRelation()->getRelation());

        Assert::same("updateDate", $properties[12]->getName());
        Assert::same("art_update_date", $properties[12]->getColumn()->getName());
    }


}

(new Annotation())->run();