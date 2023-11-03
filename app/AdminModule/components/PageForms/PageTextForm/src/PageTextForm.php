<?php

declare(strict_types = 1);

namespace App\Components\PageTextForm;

use App\Components\PageBaseForm\PageBaseForm;
use App\Components\PageBaseForm\PageValues;
use App\Helpers\Summernote;
use App\Page\PageEntity;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageTextForm extends PageBaseForm
{


    /** @var string */
    protected $pageType = PageEntity::TEXT_TYPE;



    /**
     * @param Form $form
     */
    protected function formConfiguration(Form $form)
    {
        $form->addTextArea("content", "Obsah")
            ->setAttribute("class", "form-control")
			->setHtmlId('ckEditor')
            ->setDefaultValue($this->pageEntity instanceof PageEntity ? $this->pageEntity->getContent() : NULL)
            ->setEmptyValue(Summernote::EMPTY_STRING_VALUE);
    }



    /**
     * @param Form $form
     * @return PageValues
     */
    public function getPageValues(Form $form) : PageValues
    {
        $pageValues = parent::getPageValues($form);
        $pageValues->content = $form->getValues()->content;
        return $pageValues;
    }
}