<?php

namespace App\Components\PageBaseForm;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageValues extends NObject
{


    public $languageId;
    public $type;
    public $parentPageId;
    public $name;
    public $content;
    public $url;
    public $titleSeo;
    public $descriptionSeo;
    public $setting;
    public $status;
    public $template;
    public $menuLocation;
    public $titleOg;
    public $descriptionOg;
}