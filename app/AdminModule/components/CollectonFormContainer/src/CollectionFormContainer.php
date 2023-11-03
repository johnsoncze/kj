<?php

namespace App\Components\CollectionFormContainer;

use App\Components\BaseFormContainer;
use App\Helpers\Images;
use Nette\Application\UI\Form;


class CollectionFormContainer extends BaseFormContainer
{
		public $categoryEntity;

    /** @var string */
    const NAME = "collectionForm";

    protected function configure()
    {
        $this->addText("collectionSubname", "Podtitulek")
            ->setAttribute("class", "form-control")
            ->setAttribute("maxlength", "255")
            ->setNullable(true);

        $this->addTextArea("collectionPerex", "Perex")
            ->setAttribute("class", "form-control")
            ->setNullable(true);
				
        $this->addTextArea("collectionText", "Text")
            ->setAttribute("class", "form-control")
            ->setNullable(true);
				
				$this->addUpload('collectionImage', 'Obrázek pod perexem')
					->setRequired(FALSE)
					->addRule(Form::MIME_TYPE, 'Nahrávejte pouze obrázek.', Images::getMimeTypes());				
    }


    /**
     * @return void
     */
    public function render()
    {
        $template = $this->getTemplate();
        $template->name = self::NAME;
        $template->parentForm = $this->getParent();
        $template->categoryEntity = $this->categoryEntity;
        $template->render(__DIR__ . "/default.latte");
    }


}