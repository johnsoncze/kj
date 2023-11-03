<?php

namespace App\Tests\Mappero;

require_once __DIR__ . "/../../bootstrap.php";

use App\Article\ArticleEntity;
use App\Tests\BaseTestCase;
use Ricaefeliz\Mappero\Annotations\Annotation;
use Ricaefeliz\Mappero\Annotations\AnnotationManager;
use Ricaefeliz\Mappero\Annotations\Relation;
use Ricaefeliz\Mappero\Annotations\Translation;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class AnnotationManagerTest extends BaseTestCase
{


    public function testGetArticleEntityAnnotationFromAnnotationManager()
    {
        $am = new \Ricaefeliz\Mappero\Annotations\AnnotationManager();
        $annotation = $am->getAnnotation(ArticleEntity::class);

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



    public function testGetAnnotation()
    {
        $am = new AnnotationManager();
        $annotation = $am->getAnnotation(Entity1::class);
        $oneToMany = $annotation->getPropertyByName("entities2")->getRelation();
        $oneToOne = $annotation->getPropertyByName("entity3")->getRelation();

        Assert::same("table_123", $annotation->getTable()->getName());

        Assert::null($annotation->getPropertyByName("entities2")->getColumn());
        Assert::null($annotation->getPropertyByName("entity3")->getColumn());

        Assert::type(Annotation::class, $annotation);
        Assert::same(4, count($annotation->getProperties()));
        Assert::same("id", $annotation->getPrimaryProperty()->getName());
        Assert::same("p_id", $annotation->getPrimaryProperty()->getColumn()->getName());

        Assert::type(Relation::class, $oneToMany);
        Assert::same(Relation::ONE_TO_MANY, $oneToMany->getRelation());
        Assert::same('App\Tests\Mappero\Entity2', $oneToMany->getRelationEntity());
        Assert::type(Translation::class, $annotation->getPropertyByName("entities2")->getTranslation());

        Assert::type(Relation::class, $oneToOne);
        Assert::same(Relation::ONE_TO_ONE, $oneToOne->getRelation());
        Assert::same('App\Tests\Mappero\Entity3', $oneToOne->getRelationEntity());
    }
}

(new AnnotationManagerTest())->run();