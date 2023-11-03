<?php

namespace App\Extensions\Grido;

use Grido\Components\Filters\Filter;
use Grido\Grid;
use Grido\Translations\FileTranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class GridoFactory
{


    /**
     * Create Grido
     * @return \Grido\Grid
     */
    public function create()
    {
        $grido = new Grid();
        $grido->setFilterRenderType("inner");
        $grido->getTranslator()->setLang("cs");
        $grido->getTablePrototype()->class = "table table-striped table-hover";
        $grido->templateFile = __DIR__ . "/Grido.latte";
        return $grido;
    }



	/**
	 * Create Grido
	 * @return \Grido\Grid
	 */
	public function createFrontend()
	{
		$grido = new Grid();
		$grido->setFilterRenderType(Filter::RENDER_OUTER);
		$grido->getTranslator()->setLang("cs");
		$grido->getTablePrototype()->class = "table table-responsive";
		$grido->setTranslator(new FileTranslator('cs', include __DIR__ . '/Translations/cs.php'));
		$grido->templateFile = __DIR__ . '/GridoFrontend.latte';
		return $grido;
	}
}